<?php

namespace Filament\Actions\Concerns;

use Filament\Actions\EditAction;
use Filament\Forms\EditConflictHandler;
use Filament\Schemas\Schema;
use Filament\Schemas\StateComparison;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Livewire\Attributes\Locked;
use LogicException;

trait InteractsWithActionFormConflicts
{
    /** @var array<int, array<string, mixed>> */
    #[Locked]
    public array $actionFormConflictStates = [];

    /** @var array<string, array<string, mixed>> */
    public array $actionFormConflictPreviewState = [];

    /** @var array<int, StateComparison> */
    protected array $actionFormComparisons = [];

    protected function rememberActionFormConflictBaseline(EditAction $action): void
    {
        $handler = $action->getEditConflictHandler();

        if (! $handler) {
            return;
        }

        $record = $action->getRecord();
        $this->actionFormConflictStates[$action->getNestingIndex()] = [
            'record' => [$record::class, (string) $record->getKey(), $record->getConnection()->getName()],
            'context' => $this->getActionFormConflictContext($action),
            'base' => $handler->snapshot($this->makeIsolatedActionConflictForm($action))['state'],
            'outcome' => null,
        ];
    }

    /** @return array<array<string, mixed>> */
    protected function getActionFormConflictContext(EditAction $action): array
    {
        return array_map(
            static fn (array $mountedAction): array => Arr::only($mountedAction, ['name', 'context', 'arguments']),
            array_slice($this->mountedActions, 0, $action->getNestingIndex() + 1),
        );
    }

    /** @internal */
    #[Unexposed]
    public function authorizeActionFormConflictRecord(EditAction $action, Model $lockedRecord): void
    {
        $state = $this->actionFormConflictStates[$action->getNestingIndex()] ?? null;

        abort_unless(
            $state &&
            (json_encode($state['context'], JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION) === json_encode($this->getActionFormConflictContext($action), JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION)) &&
            ($state['record'] === [$lockedRecord::class, (string) $lockedRecord->getKey(), $lockedRecord->getConnection()->getName()]),
            409,
        );

        $record = $this->getActionFormConflictRecordForUpdate($action, $lockedRecord);

        abort_unless($record instanceof Model && $record->is($lockedRecord), 404);

        foreach (array_slice($this->getMountedActions(), 0, $action->getNestingIndex() + 1) as $recordAction) {
            $actionRecord = $recordAction->getRecord();

            if ((! $actionRecord instanceof Model) || (! $actionRecord->is($record))) {
                continue;
            }

            if ($recordAction->hasCustomRecordResolver()) {
                $actionRecord = $recordAction->getRecord(shouldLockForUpdate: true);
                abort_unless(($actionRecord instanceof Model) && ($actionRecord::class === $lockedRecord::class) && $actionRecord->is($lockedRecord), 404);
            } else {
                $recordAction->record($record);
            }
        }

        abort_unless($this->resolveActionForEventListener($action->getNestingIndex()) === $action, 404);
        $this->getMountedActionSchema(mountedAction: $action)?->model($action->getRecord());
    }

    protected function getActionFormConflictRecordForUpdate(EditAction $action, Model $lockedRecord): ?Model
    {
        foreach (array_reverse(array_slice($this->mountedActions, 0, $action->getNestingIndex() + 1), preserve_keys: true) as $index => $mountedAction) {
            $context = $mountedAction['context'] ?? [];

            if (! ($this instanceof HasTable) || (! ($context['table'] ?? false)) || (! isset($context['recordKey']))) {
                continue;
            }

            $tableRecord = $this->getAction(array_slice($this->mountedActions, 0, $index + 1), isMounting: false)?->getRecord();

            if (($tableRecord instanceof Model) && $tableRecord->is($lockedRecord)) {
                return $this->getTableRecordForUpdate((string) $context['recordKey']);
            }
        }

        $recordQueryAction = null;

        foreach (array_slice($this->mountedActions, 0, $action->getNestingIndex() + 1) as $index => $mountedAction) {
            $recordAction = $this->getAction(array_slice($this->mountedActions, 0, $index + 1), isMounting: false);
            $record = $recordAction?->getRecord();

            if ((! $record instanceof Model) || (! $record->is($lockedRecord))) {
                continue;
            }

            if ($recordAction->hasCustomRecordResolver()) {
                $recordQueryAction = $recordAction;

                continue;
            }

            if (! $recordAction->hasRecord()) {
                throw new LogicException('Edit conflict protection requires a native table or resource-page record, or an explicitly configured model. Inherited schema records need a scoped locking read.');
            }
        }

        if ($recordQueryAction) {
            $record = $recordQueryAction->getRecord(shouldLockForUpdate: true);

            return ($record instanceof Model) ? $record : null;
        }

        return $lockedRecord->newQuery()->whereKey($lockedRecord->getKey())->lockForUpdate()->first();
    }

    protected function makeIsolatedActionConflictForm(EditAction $action, bool $shouldFill = false): Schema
    {
        $owner = clone $this;
        $owner->cachedSchemas = [];
        $owner->isCachingSchemas = false;
        $owner->discoveredSchemaNames = [];
        $owner->cachedActions = [];
        $owner->cachedMountedActions = null;

        if ($owner instanceof HasTable) {
            $owner->table = $owner->table($owner->makeTable());
        }

        // A mounting action can hold an entry by reference. Do not retain that
        // reference in an isolated owner across Livewire dehydration.
        $owner->mountedActions = array_map(static fn (array $mountedAction): array => [...$mountedAction], array_slice($this->mountedActions, 0, $action->getNestingIndex() + 1));
        $owner->actionFormConflictStates = array_map(static fn (array $state): array => [...$state], $this->actionFormConflictStates);
        $owner->cacheTraitActions();
        $isolatedAction = $owner->getAction($owner->mountedActions);
        $isolatedAction->record(clone $action->getRecord());
        $form = $owner->getMountedActionSchema(mountedAction: $isolatedAction);

        if ($shouldFill) {
            $isolatedAction->refillForm(['form' => $form, 'schema' => $form]);
        }

        return $form;
    }

    protected function getActionFormStateComparison(EditAction $action): StateComparison
    {
        $handler = $action->getEditConflictHandler();
        $draft = $handler->snapshot($this->makeIsolatedActionConflictForm($action));
        $current = $handler->snapshot($this->makeIsolatedActionConflictForm($action, shouldFill: true));
        $this->actionFormConflictPreviewState = ['draft' => $draft['rawState'], 'current' => $current['rawState']];
        unset($this->cachedSchemas['draftActionFormConflictPreview'], $this->cachedSchemas['currentActionFormConflictPreview']);

        return $handler->compare($this->actionFormConflictStates[$action->getNestingIndex()]['base'], $draft, $current);
    }

    protected function refreshActionFormForRecordUpdate(EditAction $action): void
    {
        if ((! $action->getEditConflictHandler()) || (($this->actionFormConflictStates[$action->getNestingIndex()]['outcome'] ?? null) !== null)) {
            return;
        }

        try {
            $action->beginEditConflictTransaction();
            $comparison = $this->getActionFormStateComparison($action)->ignoredStatePaths([]);
            $this->actionFormConflictStates[$action->getNestingIndex()]['base'] = $action->getEditConflictHandler()->acceptIncomingState($this->getMountedActionSchema(mountedAction: $action), $comparison, $this->actionFormConflictPreviewState['current']);
            $this->actionFormConflictPreviewState = [];
        } finally {
            $action->rollBackDatabaseTransaction();
        }
    }

    /** @internal */
    #[Unexposed]
    public function checkActionFormConflicts(EditAction $action): bool
    {
        $comparison = $this->getActionFormStateComparison($action);
        $this->actionFormComparisons[$action->getNestingIndex()] = $comparison;
        $state = &$this->actionFormConflictStates[$action->getNestingIndex()];
        $state['outcome'] = $comparison->hasConflicts() ? $comparison->toArray() : null;

        if ($state['outcome'] !== null) {
            $this->mountAction('resolveFormConflicts');

            return false;
        }

        $state['base'] = $action->getEditConflictHandler()->acceptIncomingState($this->getMountedActionSchema(mountedAction: $action), $comparison, $this->actionFormConflictPreviewState['current']);

        return true;
    }

    /** @internal */
    #[Unexposed]
    public function getActionEditConflictHandler(EditAction $action): EditConflictHandler
    {
        return $action->getEditConflictHandler()->context(
            fn (): Schema => $this->getMountedActionSchema(mountedAction: $action),
            fn (): ?array => $this->actionFormConflictStates[$action->getNestingIndex()]['outcome'] ?? null,
            fn (string $source): Schema => $this->getSchema($source === 'draft' ? 'draftActionFormConflictPreview' : 'currentActionFormConflictPreview'),
            resolveMethod: 'resolveActionFormConflict',
            discardMethod: 'discardActionFormChanges',
        );
    }

    protected function draftActionFormConflictPreview(): Schema
    {
        return $this->makeActionFormConflictPreview('draft');
    }

    protected function currentActionFormConflictPreview(): Schema
    {
        return $this->makeActionFormConflictPreview('current');
    }

    protected function makeActionFormConflictPreview(string $source): Schema
    {
        if ((count($this->mountedActions ?? []) < 2) || (($this->mountedActions[array_key_last($this->mountedActions)]['name'] ?? null) !== 'resolveFormConflicts')) {
            return Schema::make($this)->components([]);
        }

        $action = $this->getAction(array_slice($this->mountedActions, 0, -1), isMounting: false);

        if (! ($action instanceof EditAction) || (! $action->getEditConflictHandler())) {
            return Schema::make($this)->components([]);
        }

        $form = $this->makeIsolatedActionConflictForm($action);
        $owner = $form->getLivewire();
        data_set($owner, $form->getStatePath(), $this->actionFormConflictPreviewState[$source] ?? []);

        return $action->getEditConflictHandler()->preparePreview($form, $source, "actionFormConflictPreviewState.{$source}")
            ->key("{$source}ActionFormConflictPreview");
    }

    #[Exposed]
    public function resolveActionFormConflict(string $statePath, string $source): void
    {
        $action = $this->getMountedAction(count($this->mountedActions) - 2);

        if (($this->getMountedAction()?->getName() !== 'resolveFormConflicts') || (! ($action instanceof EditAction)) || (! $handler = $action->getEditConflictHandler())) {
            return;
        }

        $state = &$this->actionFormConflictStates[$action->getNestingIndex()];

        if (($state['outcome'] ?? null) === null) {
            return;
        }

        try {
            $action->beginEditConflictTransaction();
            $comparison = $this->getActionFormStateComparison($action);
            $comparison->statePathGroups(array_intersect_key($comparison->getStatePathGroups(), $state['outcome']['statePathGroups']));
            $base = $handler->acceptChoice($this->getMountedActionSchema(mountedAction: $action), $comparison, $state['outcome'], $this->actionFormConflictPreviewState['current'], $statePath, $source);

            if ($base !== null) {
                $state['base'] = $base;
                $comparison = $this->getActionFormStateComparison($action);
            }

            $state['outcome'] = $comparison->hasConflicts() ? $comparison->toArray() : null;
        } finally {
            $action->rollBackDatabaseTransaction();
        }

        $this->syncActionFormConflicts($action);
    }

    #[Exposed]
    public function discardActionFormChanges(): void
    {
        $action = $this->getMountedAction(count($this->mountedActions) - 2);

        if (($this->getMountedAction()?->getName() !== 'resolveFormConflicts') || (! ($action instanceof EditAction)) || (! $action->getEditConflictHandler())) {
            return;
        }

        try {
            $action->beginEditConflictTransaction();
            $currentForm = $this->makeIsolatedActionConflictForm($action, shouldFill: true);
            data_set($this, $this->getMountedActionSchema(mountedAction: $action)->getStatePath(), $currentForm->getRawState());
            $this->rememberActionFormConflictBaseline($action);
        } finally {
            $action->rollBackDatabaseTransaction();
        }

        $this->syncActionFormConflicts($action);
    }

    protected function syncActionFormConflicts(EditAction $action): void
    {
        if ($this->actionFormConflictStates[$action->getNestingIndex()]['outcome'] === null) {
            $this->unmountAction(cancelParentActions: false);
            $this->actionFormConflictPreviewState = [];
        } else {
            $conflictAction = $this->getMountedAction();
            $this->forgetCachedMountedActionSchemas($conflictAction->getNestingIndex());
            $this->cacheSchema("mountedActionSchema{$conflictAction->getNestingIndex()}", $this->getMountedActionSchema(mountedAction: $conflictAction));
        }

        $this->forceRender();
    }
}
