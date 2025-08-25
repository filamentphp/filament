<?php

namespace Filament\Tables\Concerns;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

trait HasBulkActions
{
    /**
     * @var array<int | string>
     */
    public array $selectedTableRecords = [];

    /**
     * @var array<int | string>
     */
    public array $deselectedTableRecords = [];

    public bool $isTrackingDeselectedTableRecords = false;

    protected EloquentCollection | Collection | LazyCollection $cachedSelectedTableRecords;

    /**
     * @deprecated Use the `callMountedAction()` method instead.
     *
     * @param  array<string, mixed>  $arguments
     */
    public function callMountedTableBulkAction(array $arguments = []): mixed
    {
        return $this->callMountedAction($arguments);
    }

    /**
     * @deprecated Use the `mountAction()` method instead.
     *
     * @param  array<int | string> | null  $selectedRecords
     */
    public function mountTableBulkAction(string $name, ?array $selectedRecords = null): mixed
    {
        if ($selectedRecords !== null) {
            $this->selectedTableRecords = $selectedRecords;
        }

        return $this->mountAction($name, context: ['table' => true, 'bulk' => true]);
    }

    /**
     * @deprecated Use the `replaceMountedAction()` method instead.
     *
     * @param  array<int | string> | null  $selectedRecords
     */
    public function replaceMountedTableBulkAction(string $name, ?array $selectedRecords = null): void
    {
        if ($selectedRecords !== null) {
            $this->selectedTableRecords = $selectedRecords;
        }

        $this->replaceMountedAction($name, context: ['table' => true, 'bulk' => true]);
    }

    /**
     * @deprecated Use the `mountedActionShouldOpenModal()` method instead.
     */
    public function mountedTableBulkActionShouldOpenModal(?BulkAction $mountedBulkAction = null): bool
    {
        return $this->mountedActionShouldOpenModal($mountedBulkAction);
    }

    /**
     * @deprecated Use the `mountedActionHasSchema()` method instead.
     */
    public function mountedTableBulkActionHasForm(?BulkAction $mountedBulkAction = null): bool
    {
        return $this->mountedActionHasSchema($mountedBulkAction);
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
            if (! $this->getTable()->hasQuery()) {
                /** @phpstan-ignore-next-line */
                return $this->getTableRecords()->keys()->all();
            }

            $records = $this->getTable()->selectsCurrentPageOnly() ?
                $this->getTableRecords()->pluck($query->getModel()->getKeyName()) :
                $query->toBase()->pluck($query->getModel()->getQualifiedKeyName());

            /** @phpstan-ignore-next-line */
            return $records->map(fn ($key): string => (string) $key)->all();
        }

        $records = $this->getTable()->selectsCurrentPageOnly() ?
            $this->getTableRecords() :
            $query->get();

        return $records->reduce(
            function (array $carry, Model | array $record, string $key): array {
                if (! $this->getTable()->isRecordSelectable($record)) {
                    return $carry;
                }

                $carry[] = ($record instanceof Model) ? ((string) $record->getKey()) : $key;

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
                $query->toBase()->pluck($query->getModel()->getQualifiedKeyName());

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
                ->filter(fn (Model | array $record): bool => $this->getTable()->isRecordSelectable($record))
                ->count();
        }

        if ($this->getTable()->selectsCurrentPageOnly()) {
            return $this->cachedTableRecords->count();
        }

        if ($this->cachedTableRecords instanceof LengthAwarePaginator) {
            return $this->cachedTableRecords->total();
        }

        return $this->getFilteredTableQuery()?->count() ?? $this->cachedTableRecords->count();
    }

    public function getSelectedTableRecords(bool $shouldFetchSelectedRecords = true, ?int $chunkSize = null): EloquentCollection | Collection | LazyCollection
    {
        if (isset($this->cachedSelectedTableRecords)) {
            return $this->cachedSelectedTableRecords;
        }

        $table = $this->getTable();

        if ($shouldFetchSelectedRecords && $table->getRelationship() instanceof BelongsToMany && $table->allowsDuplicates()) {
            return $this->cachedSelectedTableRecords = $this->hydratePivotRelationForTableRecords(
                $this->getSelectedTableRecordsQuery($shouldFetchSelectedRecords, $chunkSize)->get(),
            );
        }

        if (! $table->hasQuery()) {
            $resolveSelectedRecords = $table->getResolveSelectedRecordsCallback();

            $resolvedSelectedRecords = $resolveSelectedRecords ?
                $table->evaluate($resolveSelectedRecords, [
                    'keys' => $this->selectedTableRecords,
                    'records' => $this->selectedTableRecords,
                    'deselectedKeys' => $this->deselectedTableRecords,
                    'deselectedRecords' => $this->deselectedTableRecords,
                    'isTrackingDeselectedKeys' => $this->isTrackingDeselectedTableRecords,
                    'isTrackingDeselectedRecords' => $this->isTrackingDeselectedTableRecords,
                ]) :
                ($this->isTrackingDeselectedTableRecords ? $this->getTableRecords()->except($this->deselectedTableRecords) : $this->getTableRecords()->only($this->selectedTableRecords));

            $maxSelectableRecords = $table->getMaxSelectableRecords();

            if ($maxSelectableRecords && ($resolvedSelectedRecords->count() > $maxSelectableRecords)) {
                throw new Exception("The total count of selected records [{$resolvedSelectedRecords->count()}] must not exceed the maximum selectable records limit [{$maxSelectableRecords}].");
            }

            return $this->cachedSelectedTableRecords = $resolvedSelectedRecords;
        }

        $query = $this->getSelectedTableRecordsQuery($shouldFetchSelectedRecords, $chunkSize);

        if (! $chunkSize) {
            $this->applySortingToTableQuery($query);
        }

        if (! $shouldFetchSelectedRecords) {
            return $this->cachedSelectedTableRecords = $query->toBase()->pluck($query->getModel()->getQualifiedKeyName());
        }

        if ($chunkSize) {
            return $this->cachedSelectedTableRecords = $query->lazyById($chunkSize);
        }

        return $this->cachedSelectedTableRecords = $query->get();
    }

    public function getSelectedTableRecordsQuery(bool $shouldFetchSelectedRecords = true, ?int $chunkSize = null): Builder
    {
        $table = $this->getTable();
        $maxSelectableRecords = $table->getMaxSelectableRecords();

        if (! ($table->getRelationship() instanceof BelongsToMany && $table->allowsDuplicates())) {
            if ($this->isTrackingDeselectedTableRecords) {
                $query = $table->getQuery()->whereKeyNot($this->deselectedTableRecords);
            } else {
                $query = $table->getQuery()->whereKey($this->selectedTableRecords);
            }

            if ($maxSelectableRecords) {
                $query->limit($maxSelectableRecords);
            }

            if (! $chunkSize) {
                $this->applySortingToTableQuery($query);
            }

            if ($shouldFetchSelectedRecords) {
                foreach ($this->getTable()->getColumns() as $column) {
                    $column->applyEagerLoading($query);
                    $column->applyRelationshipAggregates($query);
                }
            }

            if ($table->shouldDeselectAllRecordsWhenFiltered()) {
                $this->filterTableQuery($query);
            }

            return $query;
        }

        /** @var BelongsToMany $relationship */
        $relationship = $table->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        if ($this->isTrackingDeselectedTableRecords) {
            $relationship->wherePivotNotIn($pivotKeyName, $this->deselectedTableRecords);
        } else {
            $relationship->wherePivotIn($pivotKeyName, $this->selectedTableRecords);
        }

        if ($maxSelectableRecords) {
            $relationship->limit($maxSelectableRecords);
        }

        if ($shouldFetchSelectedRecords) {
            foreach ($this->getTable()->getColumns() as $column) {
                $column->applyEagerLoading($relationship);
                $column->applyRelationshipAggregates($relationship);
            }
        }

        return $table->selectPivotDataInQuery($relationship);
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

    /**
     * @deprecated Use the `getMountedAction()` method instead.
     */
    public function getMountedTableBulkAction(): ?Action
    {
        return $this->getMountedAction();
    }

    /**
     * @deprecated Use the `getMountedActionSchema()` method instead.
     */
    public function getMountedTableBulkActionForm(?BulkAction $mountedBulkAction = null): ?Schema
    {
        return $this->getMountedActionSchema(0, $mountedBulkAction);
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
}
