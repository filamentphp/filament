<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Support\Exceptions\Cancel;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Throwable;

use function Livewire\store;

/**
 * @property Form $mountedTableBulkActionForm
 */
trait HasBulkActions
{
    /**
     * @var array<int | string>
     */
    public array $selectedTableRecords = [];

    public ?string $mountedTableBulkAction = null;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $mountedTableBulkActionData = [];

    protected EloquentCollection | Collection $cachedSelectedTableRecords;

    protected function configureTableBulkAction(BulkAction $action): void
    {
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function callMountedTableBulkAction(array $arguments = []): mixed
    {
        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $action->mergeArguments($arguments);

        $form = $this->getMountedTableBulkActionForm(mountedBulkAction: $action);

        $result = null;

        $originallyMountedAction = $this->mountedTableBulkAction;

        try {
            $action->beginDatabaseTransaction();

            if ($this->mountedTableBulkActionHasForm(mountedBulkAction: $action)) {
                $action->callBeforeFormValidated();

                $action->formData($form->getState());

                $action->callAfterFormValidated();
            }

            $action->callBefore();

            $result = $action->call([
                'form' => $form,
            ]);

            $result = $action->callAfter() ?? $result;

            $action->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $action->rollBackDatabaseTransaction() :
                $action->commitDatabaseTransaction();

            return null;
        } catch (Cancel $exception) {
            $exception->shouldRollbackDatabaseTransaction() ?
                $action->rollBackDatabaseTransaction() :
                $action->commitDatabaseTransaction();
        } catch (ValidationException $exception) {
            $action->rollBackDatabaseTransaction();

            if (! $this->mountedTableBulkActionShouldOpenModal(mountedBulkAction: $action)) {
                $action->resetArguments();
                $action->resetFormData();

                $this->unmountTableBulkAction();
            }

            throw $exception;
        } catch (Throwable $exception) {
            $action->rollBackDatabaseTransaction();

            throw $exception;
        }

        if (store($this)->has('redirect')) {
            return $result;
        }

        $action->resetArguments();
        $action->resetFormData();

        // If the action was replaced while it was being called,
        // we don't want to unmount it.
        if ($originallyMountedAction !== $this->mountedTableBulkAction) {
            return null;
        }

        $this->unmountTableBulkAction();

        return $result;
    }

    /**
     * @param  array<int | string> | null  $selectedRecords
     */
    public function mountTableBulkAction(string $name, ?array $selectedRecords = null): mixed
    {
        $this->mountedTableBulkAction = $name;

        if ($selectedRecords !== null) {
            $this->selectedTableRecords = $selectedRecords;
        }

        $action = $this->getMountedTableBulkAction();

        if (! $action) {
            return null;
        }

        if ($action->isDisabled()) {
            return null;
        }

        $this->cacheMountedTableBulkActionForm(mountedBulkAction: $action);

        try {
            $hasForm = $this->mountedTableBulkActionHasForm(mountedBulkAction: $action);

            if ($hasForm) {
                $action->callBeforeFormFilled();
            }

            $action->mount([
                'form' => $this->getMountedTableBulkActionForm(mountedBulkAction: $action),
            ]);

            if ($hasForm) {
                $action->callAfterFormFilled();
            }
        } catch (Halt $exception) {
            return null;
        } catch (Cancel $exception) {
            $this->resetMountedTableBulkActionProperties();

            return null;
        }

        if (! $this->mountedTableBulkActionShouldOpenModal(mountedBulkAction: $action)) {
            return $this->callMountedTableBulkAction();
        }

        $this->resetErrorBag();

        $this->openTableBulkActionModal();

        return null;
    }

    protected function cacheMountedTableBulkActionForm(?BulkAction $mountedBulkAction = null): void
    {
        $this->cacheForm(
            'mountedTableBulkActionForm',
            fn () => $this->getMountedTableBulkActionForm($mountedBulkAction),
        );
    }

    /**
     * @param  array<int | string> | null  $selectedRecords
     */
    public function replaceMountedTableBulkAction(string $name, ?array $selectedRecords = null): void
    {
        $selectedRecords ??= $this->selectedTableRecords;

        $this->resetMountedTableBulkActionProperties();
        $this->mountTableBulkAction($name, $selectedRecords);
    }

    protected function resetMountedTableBulkActionProperties(): void
    {
        $this->mountedTableBulkAction = null;
        $this->selectedTableRecords = [];
    }

    public function mountedTableBulkActionShouldOpenModal(?BulkAction $mountedBulkAction = null): bool
    {
        return ($mountedBulkAction ?? $this->getMountedTableBulkAction())->shouldOpenModal(
            checkForFormUsing: $this->mountedTableBulkActionHasForm(...),
        );
    }

    public function unmountTableBulkAction(bool $shouldCloseModal = true): void
    {
        $this->resetMountedTableBulkActionProperties();

        if ($shouldCloseModal) {
            $this->closeTableBulkActionModal();
        }
    }

    public function mountedTableBulkActionHasForm(?BulkAction $mountedBulkAction = null): bool
    {
        return (bool) count($this->getMountedTableBulkActionForm(mountedBulkAction: $mountedBulkAction)?->getComponents() ?? []);
    }

    public function deselectAllTableRecords(): void
    {
        $this->dispatch('deselectAllTableRecords');
    }

    /**
     * @return array<string>
     */
    public function getAllSelectableTableRecordKeys(): array
    {
        $query = $this->getFilteredTableQuery();

        if (! $this->getTable()->checksIfRecordIsSelectable()) {
            $records = $this->getTable()->selectsCurrentPageOnly() ?
                $this->getTableRecords()->pluck($query->getModel()->getKeyName()) :
                $query->pluck($query->getModel()->getQualifiedKeyName());

            return $records
                ->map(fn ($key): string => (string) $key)
                ->all();
        }

        $records = $this->getTable()->selectsCurrentPageOnly() ?
            $this->getTableRecords() :
            $query->get();

        return $records->reduce(
            function (array $carry, Model $record): array {
                if (! $this->getTable()->isRecordSelectable($record)) {
                    return $carry;
                }

                $carry[] = (string) $record->getKey();

                return $carry;
            },
            initial: [],
        );
    }

    /**
     * @return array<string>
     */
    public function getGroupedSelectableTableRecordKeys(string $group): array
    {
        $query = $this->getFilteredTableQuery();

        $tableGrouping = $this->getTableGrouping();

        $tableGrouping->scopeQueryByKey($query, $group);

        if (! $this->getTable()->checksIfRecordIsSelectable()) {
            $records = $this->getTable()->selectsCurrentPageOnly() ?
                /** @phpstan-ignore-next-line */
                $this->getTableRecords()
                    ->filter(fn (Model $record): bool => $tableGrouping->getStringKey($record) === $group)
                    ->pluck($query->getModel()->getKeyName()) :
                $query->pluck($query->getModel()->getQualifiedKeyName());

            return $records
                ->map(fn ($key): string => (string) $key)
                ->all();
        }

        $records = $this->getTable()->selectsCurrentPageOnly() ?
            $this->getTableRecords()->filter(
                fn (Model $record) => $tableGrouping->getStringKey($record) === $group,
            ) :
            $query->get();

        return $records->reduce(
            function (array $carry, Model $record): array {
                if (! $this->getTable()->isRecordSelectable($record)) {
                    return $carry;
                }

                $carry[] = (string) $record->getKey();

                return $carry;
            },
            initial: [],
        );
    }

    public function getAllSelectableTableRecordsCount(): int
    {
        if ($this->getTable()->checksIfRecordIsSelectable()) {
            /** @var Collection $records */
            $records = $this->getTable()->selectsCurrentPageOnly() ?
                $this->getTableRecords() :
                $this->getFilteredTableQuery()->get();

            return $records
                ->filter(fn (Model $record): bool => $this->getTable()->isRecordSelectable($record))
                ->count();
        }

        if ($this->getTable()->selectsCurrentPageOnly()) {
            return $this->cachedTableRecords->count();
        }

        if ($this->cachedTableRecords instanceof LengthAwarePaginator) {
            return $this->cachedTableRecords->total();
        }

        return $this->getFilteredTableQuery()->count();
    }

    public function getSelectedTableRecords(bool $shouldFetchSelectedRecords = true): EloquentCollection | Collection
    {
        if (isset($this->cachedSelectedTableRecords)) {
            return $this->cachedSelectedTableRecords;
        }

        $table = $this->getTable();

        if (
            $shouldFetchSelectedRecords ||
            (! ($table->getRelationship() instanceof BelongsToMany && $table->allowsDuplicates()))
        ) {
            $query = $table->getQuery()->whereKey($this->selectedTableRecords);
            $this->applySortingToTableQuery($query);

            if ($shouldFetchSelectedRecords) {
                foreach ($this->getTable()->getColumns() as $column) {
                    $column->applyEagerLoading($query);
                    $column->applyRelationshipAggregates($query);
                }
            }

            if ($table->shouldDeselectAllRecordsWhenFiltered()) {
                $this->filterTableQuery($query);
            }

            return $this->cachedSelectedTableRecords = $shouldFetchSelectedRecords ?
                $query->get() :
                $query->pluck($query->getModel()->getQualifiedKeyName());
        }

        /** @var BelongsToMany $relationship */
        $relationship = $table->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        $relationship->wherePivotIn($pivotKeyName, $this->selectedTableRecords);

        foreach ($this->getTable()->getColumns() as $column) {
            $column->applyEagerLoading($relationship);
            $column->applyRelationshipAggregates($relationship);
        }

        return $this->cachedSelectedTableRecords = $this->hydratePivotRelationForTableRecords(
            $table->selectPivotDataInQuery($relationship)->get(),
        );
    }

    protected function closeTableBulkActionModal(): void
    {
        $this->dispatch('close-modal', id: "{$this->getId()}-table-bulk-action");
    }

    protected function openTableBulkActionModal(): void
    {
        $this->dispatch('open-modal', id: "{$this->getId()}-table-bulk-action");
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function shouldSelectCurrentPageOnly(): bool
    {
        return false;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function shouldDeselectAllRecordsWhenTableFiltered(): bool
    {
        return true;
    }

    public function getMountedTableBulkAction(): ?BulkAction
    {
        if (! $this->mountedTableBulkAction) {
            return null;
        }

        return $this->getTable()->getBulkAction($this->mountedTableBulkAction);
    }

    public function getMountedTableBulkActionForm(?BulkAction $mountedBulkAction = null): ?Form
    {
        $mountedBulkAction ??= $this->getMountedTableBulkAction();

        if (! $mountedBulkAction) {
            return null;
        }

        if ((! $this->isCachingForms) && $this->hasCachedForm('mountedTableBulkActionForm')) {
            return $this->getForm('mountedTableBulkActionForm');
        }

        return $mountedBulkAction->getForm(
            $this->makeForm()
                ->model($this->getTable()->getModel())
                ->statePath('mountedTableBulkActionData')
                ->operation($this->mountedTableBulkAction),
        );
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<BulkAction>
     */
    protected function getTableBulkActions(): array
    {
        return [];
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function isTableRecordSelectable(): ?Closure
    {
        return null;
    }

    public function mountedTableBulkActionInfolist(): Infolist
    {
        return $this->getMountedTableBulkAction()->getInfolist();
    }
}
