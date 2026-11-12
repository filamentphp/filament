<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\EditConflictHandler;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Schemas\StateComparison;
use Filament\Support\Components\Attributes\Exposed;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use LogicException;
use Throwable;

trait ResolvesFormConflicts
{
    use CanUseDatabaseTransactions {
        beginDatabaseTransaction as beginBaseDatabaseTransaction;
        commitDatabaseTransaction as commitBaseDatabaseTransaction;
        rollBackDatabaseTransaction as rollBackBaseDatabaseTransaction;
    }

    /** @var array<string, mixed> */
    #[Locked]
    public array $baseFormState = [];

    /** @var array<string, mixed> */
    #[Locked]
    public array $savedFormData = [];

    /** @var array<string, mixed> | null */
    #[Locked]
    public ?array $staleFormState = null;

    /** @var array<string, array<string, mixed>> */
    public array $formConflictPreviewState = [];

    protected ?int $recordSaveTransactionLevel = null;

    protected function getFormEditConflictHandler(): ?EditConflictHandler
    {
        return EditConflictHandler::make();
    }

    protected function getFormStateComparison(): StateComparison
    {
        $draft = $this->getFormStateComparisonSnapshot();
        $current = $this->getFormStateComparisonSnapshot($this->getRecord());
        $this->formConflictPreviewState = ['draft' => $draft['rawState'], 'current' => $current['rawState']];
        unset($this->cachedSchemas['draftFormConflictPreview'], $this->cachedSchemas['currentFormConflictPreview']);

        return $this->getFormEditConflictHandler()->compare($this->baseFormState, $draft, $current);
    }

    /** @return array<string, mixed> */
    protected function getFormStateForStaleEditComparison(?Model $record = null): array
    {
        return $this->getFormStateComparisonSnapshot($record)['state'];
    }

    /** @return array{state: array<string, mixed>, rawState: array<string, mixed>, statePathGroups: array<string, array<string>>, orderedStatePaths: array<string>} */
    protected function getFormStateComparisonSnapshot(?Model $record = null): array
    {
        // Use a separate form owner so reading current values cannot replace
        // the draft, its mounted actions, or cached relationship state.
        $comparison = clone $this;
        $comparison->cachedSchemas = [];
        $comparison->discoveredSchemaNames = [];
        $comparison->record = clone ($record ?? $this->getRecord());
        $comparison->record->unsetRelations();

        if ($record !== null) {
            $comparison->data = [];
            $comparison->form->fill($comparison->mutateFormDataBeforeFill($record->attributesToArray()));
        }

        return $this->getFormEditConflictHandler()->snapshot($comparison->form);
    }

    protected function draftFormConflictPreview(): Schema
    {
        return $this->makeFormConflictPreview('draft');
    }

    protected function currentFormConflictPreview(): Schema
    {
        return $this->makeFormConflictPreview('current');
    }

    protected function makeFormConflictPreview(string $source): Schema
    {
        if ($this->staleFormState === null) {
            return Schema::make($this)->components([]);
        }

        $owner = clone $this;
        $owner->cachedSchemas = [];
        $owner->isCachingSchemas = false;
        $owner->discoveredSchemaNames = [];
        $owner->record = clone $this->getRecord();
        $owner->record->unsetRelations();
        $owner->data = $this->formConflictPreviewState[$source] ?? [];

        return $this->getFormEditConflictHandler()->preparePreview($owner->form, $source, "formConflictPreviewState.{$source}");
    }

    protected function beginDatabaseTransaction(): void
    {
        if (! $this->getFormEditConflictHandler()) {
            $this->beginBaseDatabaseTransaction();

            return;
        }

        $connection = $this->getRecord()->getConnection();
        $this->recordSaveTransactionLevel = $connection->transactionLevel();
        $connection->beginTransaction();

        // Scope checks must also use a locking read: an outer MySQL
        // transaction may already have an older consistent-read snapshot.
        $this->shouldLockRecordForUpdate = true;
        $this->record = $this->resolveRecordFromLivewire($this->getFormEditConflictHandler()->lockRecord($this->getRecord()));
        $this->authorizeAccess();
        $this->form->model($this->getRecord());
    }

    protected function getLockedRecord(): Model
    {
        return $this->getRecord()->newQueryWithoutScopes()
            ->whereKey($this->getRecord()->getKey())
            ->lockForUpdate()
            ->firstOrFail();
    }

    protected function commitDatabaseTransaction(): void
    {
        if (! $this->getFormEditConflictHandler()) {
            $this->commitBaseDatabaseTransaction();

            return;
        }

        try {
            $this->getRecord()->getConnection()->commit();
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->recordSaveTransactionLevel = null;
        $this->shouldLockRecordForUpdate = false;
    }

    protected function rollBackDatabaseTransaction(): void
    {
        if (! $this->getFormEditConflictHandler()) {
            $this->rollBackBaseDatabaseTransaction();

            return;
        }

        if ($this->recordSaveTransactionLevel === null) {
            return;
        }

        $this->getRecord()->getConnection()->rollBack($this->recordSaveTransactionLevel);
        $this->recordSaveTransactionLevel = null;
        $this->shouldLockRecordForUpdate = false;
    }

    protected function getFormStateComparisonForComponent(Component $component): StateComparison
    {
        $fieldPaths = [];

        foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
            foreach ($childSchema->getFlatComponents(withActions: false, withHidden: true) as $field) {
                if ($field instanceof Field) {
                    $fieldPaths[(string) str($field->getStatePath())->after('data.')] = true;
                }
            }
        }

        if ($component instanceof Field) {
            $fieldPaths[(string) str($component->getStatePath())->after('data.')] = true;
        }

        $comparison = $this->getFormStateComparison();
        $sectionStatePaths = array_merge([], ...array_values(array_intersect_key($this->getFormStateComparisonSnapshot()['statePathGroups'], $fieldPaths)));
        $sectionGroups = [];

        foreach ($comparison->getStatePathGroups() as $group => $statePaths) {
            $overlapsSection = false;
            $isOwnedBySection = true;

            foreach ($statePaths as $statePath) {
                $isPathOwnedBySection = false;

                foreach ($sectionStatePaths as $sectionStatePath) {
                    if (($statePath === $sectionStatePath) || str_starts_with($statePath, "{$sectionStatePath}.")) {
                        $isPathOwnedBySection = true;
                        $overlapsSection = true;
                    } elseif (str_starts_with($sectionStatePath, "{$statePath}.")) {
                        $overlapsSection = true;
                    }
                }

                $isOwnedBySection = $isOwnedBySection && $isPathOwnedBySection;
            }

            if (! $overlapsSection) {
                continue;
            }

            if (! $isOwnedBySection) {
                throw new LogicException('A section save cannot split a form state comparison group. Save the whole group together.');
            }

            $sectionGroups[$group] = $statePaths;
        }

        return $comparison->statePathGroups($sectionGroups);
    }

    protected function checkForStaleEdit(StateComparison $comparison): bool
    {
        if (! $comparison->hasConflicts()) {
            $this->baseFormState = $this->getFormEditConflictHandler()->acceptIncomingState($this->form, $comparison, $this->formConflictPreviewState['current']);

            $this->staleFormState = null;
            $this->formConflictPreviewState = [];

            return true;
        }

        $this->staleFormState = $comparison->toArray();
        $this->syncFormConflictAction();

        return false;
    }

    protected function getResolveFormConflictsAction(): Action
    {
        return $this->getFormEditConflictHandler()->context(
            fn (): Schema => $this->form,
            fn (): ?array => $this->staleFormState,
            fn (string $source): Schema => $this->getSchema($source === 'draft' ? 'draftFormConflictPreview' : 'currentFormConflictPreview'),
        )->getAction();
    }

    protected function syncFormConflictAction(): void
    {
        if ($this->staleFormState !== null) {
            $action = $this->getMountedAction();

            if ($action?->getName() === 'resolveFormConflicts') {
                $this->forgetCachedMountedActionSchemas($action->getNestingIndex());
                $this->cacheSchema("mountedActionSchema{$action->getNestingIndex()}", $this->getMountedActionSchema(mountedAction: $action));
            } else {
                $this->mountAction('resolveFormConflicts', context: [
                    'schemaComponent' => $this->content->getComponent(
                        static fn (Component $component): bool => $component->getAction('resolveFormConflicts') !== null,
                        withActions: false,
                        withHidden: true,
                    )->getKey(),
                ]);
            }
        } elseif ($this->getMountedAction()?->getName() === 'resolveFormConflicts') {
            $this->unmountAction(cancelParentActions: false);
        }

        if ($this->staleFormState === null) {
            $this->formConflictPreviewState = [];
        }

        $this->forceRender();
    }

    protected function getFormStateComparisonForRecovery(): StateComparison
    {
        $comparison = $this->getFormStateComparison();

        if ($this->staleFormState !== null) {
            $comparison->statePathGroups(array_intersect_key($comparison->getStatePathGroups(), $this->staleFormState['statePathGroups']));
        }

        return $comparison;
    }

    #[Exposed]
    public function resolveFormConflict(string $statePath, string $source): void
    {
        if (($this->staleFormState === null) || (! in_array($statePath, $this->staleFormState['conflictingPaths'], true)) || (! in_array($source, ['draft', 'current'], true))) {
            return;
        }

        try {
            $this->beginDatabaseTransaction();
            $comparison = $this->getFormStateComparisonForRecovery();
            $base = $this->getFormEditConflictHandler()->acceptChoice($this->form, $comparison, $this->staleFormState, $this->formConflictPreviewState['current'], $statePath, $source);

            if ($base !== null) {
                $this->baseFormState = $base;
                $comparison = $this->getFormStateComparisonForRecovery();
            }

            $this->staleFormState = $comparison->hasConflicts() ? $comparison->toArray() : null;
        } finally {
            $this->rollBackDatabaseTransaction();
        }

        $this->syncFormConflictAction();
    }

    /**
     * @param  array<string, mixed>  $state
     * @param  array<string>  $statePaths
     */
    protected function acceptFormStateBaseline(array $state, array $statePaths): void
    {
        $this->baseFormState = $this->getFormEditConflictHandler()->acceptBaseline($this->baseFormState, $state, $statePaths);
    }

    #[Exposed]
    public function discardFormChanges(): void
    {
        if (! $this->getFormEditConflictHandler()) {
            return;
        }

        try {
            $this->beginDatabaseTransaction();
            $this->fillForm();
        } finally {
            $this->rollBackDatabaseTransaction();
        }

        $this->rememberData();
        $this->syncFormConflictAction();
    }
}
