<?php

namespace Filament\Tables\Concerns;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use LogicException;

use function Livewire\invade;

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
    #[Exposed]
    public function callMountedTableBulkAction(array $arguments = []): mixed
    {
        return $this->callMountedAction($arguments);
    }

    /**
     * @deprecated Use the `mountAction()` method instead.
     *
     * @param  array<int | string> | null  $selectedRecords
     */
    #[Exposed]
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
    #[Exposed]
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
    #[Unexposed]
    public function mountedTableBulkActionShouldOpenModal(?BulkAction $mountedBulkAction = null): bool
    {
        return $this->mountedActionShouldOpenModal($mountedBulkAction);
    }

    /**
     * @deprecated Use the `mountedActionHasSchema()` method instead.
     */
    #[Unexposed]
    public function mountedTableBulkActionHasForm(?BulkAction $mountedBulkAction = null): bool
    {
        return $this->mountedActionHasSchema($mountedBulkAction);
    }

    #[Unexposed]
    public function deselectAllTableRecords(): void
    {
        $this->dispatch('deselectAllTableRecords')->self();
    }

    /**
     * @return array<string>
     */
    #[Exposed]
    public function getAllSelectableTableRecordKeys(): array
    {
        $query = $this->getFilteredTableQuery();

        if ($query && $this->getTable()->hasPivotRecordKeys() && (! $this->getTable()->selectsCurrentPageOnly())) {
            $query = $this->applyPivotRecordKeyToTableQuery($query);
        }

        if (! $this->getTable()->checksIfRecordIsSelectable()) {
            if (! $this->getTable()->hasQuery()) {
                /** @phpstan-ignore-next-line */
                return $this->getTableRecords()->keys()->all();
            }

            if ($this->getTable()->selectsCurrentPageOnly()) {
                $records = $this->getTableRecords();
                $records = $this->getTable()->hasPivotRecordKeys() ?
                    $records->map(fn (Model $record): string => $this->getTableRecordKey($record)) :
                    $records->pluck($query->getModel()->getKeyName());
            } else {
                $records = $query->toBase()->pluck($this->getTable()->hasPivotRecordKeys() ? 'filament_pivot_key' : $query->getModel()->getQualifiedKeyName());
            }

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

                if ($record instanceof Model) {
                    $key = $this->getTable()->hasPivotRecordKeys() ?
                        $this->getTableRecordKey($record) :
                        ((string) $record->getKey());
                }

                $carry[] = $key;

                return $carry;
            },
            initial: [],
        );
    }

    /**
     * @return array<string>
     */
    #[Unexposed]
    public function getGroupedSelectableTableRecordKeys(?string $group): array
    {
        $tableGrouping = $this->getTableGrouping();

        if (! $this->getTable()->hasQuery()) {
            $records = $this->getTableRecords()
                ->filter(static fn (array $record): bool => $tableGrouping->getStringKey($record) === $group);

            if (! $this->getTable()->checksIfRecordIsSelectable()) {
                return $records
                    ->map(fn (array $record): string => $this->getTableRecordKey($record)) /** @phpstan-ignore method.notFound */
                    ->values()
                    ->all();
            }

            /** @phpstan-ignore-next-line */
            return $records->reduce(
                function (array $carry, array $record): array {
                    if (! $this->getTable()->isRecordSelectable($record)) {
                        return $carry;
                    }

                    $carry[] = $this->getTableRecordKey($record);

                    return $carry;
                },
                initial: [],
            );
        }

        $query = $this->getFilteredTableQuery();

        $tableGrouping->scopeQueryByKey($query, $group);

        if ($this->getTable()->hasPivotRecordKeys() && (! $this->getTable()->selectsCurrentPageOnly())) {
            $query = $this->applyPivotRecordKeyToTableQuery($query);
        }

        if (! $this->getTable()->checksIfRecordIsSelectable()) {
            if ($this->getTable()->selectsCurrentPageOnly()) {
                $records = $this->getTableRecords()
                    ->filter(fn (Model $record): bool => $tableGrouping->getStringKey($record) === $group); /** @phpstan-ignore method.notFound */
                $records = $this->getTable()->hasPivotRecordKeys() ?
                    $records->map(fn (Model $record): string => $this->getTableRecordKey($record)) :
                    $records->pluck($query->getModel()->getKeyName());
            } else {
                $records = $query->toBase()->pluck($this->getTable()->hasPivotRecordKeys() ? 'filament_pivot_key' : $query->getModel()->getQualifiedKeyName());
            }

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

                $carry[] = $this->getTable()->hasPivotRecordKeys() ? $this->getTableRecordKey($record) : ((string) $record->getKey());

                return $carry;
            },
            initial: [],
        );
    }

    #[Unexposed]
    public function getAllSelectableTableRecordsCount(): int
    {
        if ($this->getTable()->checksIfRecordIsSelectable()) {
            /** @var Collection $records */
            $records = $this->getTable()->selectsCurrentPageOnly() ?
                $this->getTableRecords() :
                ($this->getTable()->hasPivotRecordKeys() ?
                    $this->applyPivotRecordKeyToTableQuery($this->getFilteredTableQuery()) :
                    $this->getFilteredTableQuery())->get();

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

    #[Unexposed]
    public function getSelectedTableRecords(bool $shouldFetchSelectedRecords = true, ?int $chunkSize = null): EloquentCollection | Collection | LazyCollection
    {
        if (isset($this->cachedSelectedTableRecords)) {
            return $this->cachedSelectedTableRecords;
        }

        $table = $this->getTable();

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

            if ($table->checksIfRecordIsSelectable()) {
                $resolvedSelectedRecords = $resolvedSelectedRecords->filter(
                    fn (Model | array $record): bool => $table->isRecordSelectable($record)
                );
            }

            $maxSelectableRecords = $table->getMaxSelectableRecords();

            if ($maxSelectableRecords && ($resolvedSelectedRecords->count() > $maxSelectableRecords)) {
                throw new LogicException("The total count of selected records [{$resolvedSelectedRecords->count()}] must not exceed the maximum selectable records limit [{$maxSelectableRecords}].");
            }

            return $this->cachedSelectedTableRecords = $resolvedSelectedRecords;
        }

        $query = $this->getSelectedTableRecordsQuery(chunkSize: $chunkSize);

        if ((! $shouldFetchSelectedRecords) && (! $table->checksIfRecordIsSelectable())) {
            return $this->cachedSelectedTableRecords = $query->toBase()->pluck($query->getModel()->getQualifiedKeyName());
        }

        if ($chunkSize || (! $shouldFetchSelectedRecords)) {
            $resolvedSelectedRecords = $this->lazyTableRecordsByKey($query, $chunkSize ?? 1000);

            if ($table->getRelationship() instanceof BelongsToMany && ! $table->allowsDuplicates()) {
                $invadedRelationship = invade($table->getRelationship());

                $resolvedSelectedRecords = $resolvedSelectedRecords
                    ->tapEach(fn (Model $record) => $invadedRelationship->hydratePivotRelation([$record]));
            }
        } else {
            $resolvedSelectedRecords = $this->hydratePivotRelationForTableRecords($query->get());
        }

        if ($table->checksIfRecordIsSelectable()) {
            $resolvedSelectedRecords = $resolvedSelectedRecords->filter(
                fn (Model | array $record): bool => $table->isRecordSelectable($record)
            );
        }

        if (! $shouldFetchSelectedRecords) {
            return $this->cachedSelectedTableRecords = $resolvedSelectedRecords->pluck($query->getModel()->getKeyName())->collect();
        }

        return $this->cachedSelectedTableRecords = $resolvedSelectedRecords;
    }

    #[Unexposed]
    public function getSelectedTableRecordsQuery(bool $shouldFetchSelectedRecords = true, ?int $chunkSize = null): Builder
    {
        $table = $this->getTable();
        $hasPivotRecordKeys = $table->hasPivotRecordKeys();
        $query = $table->getQuery();

        if ($hasPivotRecordKeys) {
            $qualifiedKeyName = $this->getQualifiedTablePivotKeyName();

            if ($this->isTrackingDeselectedTableRecords) {
                $query->whereNotIn($qualifiedKeyName, $this->deselectedTableRecords);
            } else {
                $query->whereIn($qualifiedKeyName, $this->selectedTableRecords);
            }

            $table->selectPivotDataInQuery($query);
        } elseif ($this->isTrackingDeselectedTableRecords) {
            $query->whereKeyNot($this->deselectedTableRecords);
        } else {
            $query->whereKey($this->selectedTableRecords);
        }

        $shouldApplyTableColumnRelationshipAggregates = $this->shouldApplyTableColumnRelationshipAggregates;
        $this->shouldApplyTableColumnRelationshipAggregates = false;

        try {
            $query = $this->applySortingToTableQuery($query);

            if ($table->shouldDeselectAllRecordsWhenFiltered()) {
                $query = $this->filterTableQuery($query);
            }
        } finally {
            $this->shouldApplyTableColumnRelationshipAggregates = $shouldApplyTableColumnRelationshipAggregates;
        }

        // Bulk actions may use hidden aggregates too. Apply each column once,
        // independently of the aggregates normally added by sorting/filtering.
        foreach ($table->getColumns() as $column) {
            if ($shouldFetchSelectedRecords) {
                $column->applyEagerLoading($query);
            }

            $column->applyRelationshipAggregates($query);
        }

        if ($hasPivotRecordKeys) {
            $query = $this->applyPivotRecordKeyToTableQuery($query);
        }

        $query = $this->applySelectionLimitToTableQuery($query, $shouldFetchSelectedRecords);

        if (! $shouldFetchSelectedRecords) {
            $this->constrainQueryToSelectableTableRecords($query);
        }

        return $query;
    }

    protected function applySelectionLimitToTableQuery(Builder $query, bool $shouldFetchSelectedRecords): Builder
    {
        $query = $query->applyScopes()->withoutGlobalScopes();
        $maxSelectableRecords = $this->getTable()->getMaxSelectableRecords();

        if ($maxSelectableRecords) {
            $query->limit(min($query->getQuery()->limit ?? $maxSelectableRecords, $maxSelectableRecords));
        }

        if ($shouldFetchSelectedRecords || (($query->getQuery()->limit === null) && (! $query->getQuery()->offset))) {
            return $query;
        }

        // Joined writes do not reliably support `LIMIT`. Resolve the bounded
        // selection first, retaining its scopes and relationship constraints.
        $qualifiedKeyName = $this->getTable()->hasPivotRecordKeys()
            ? $this->getQualifiedTablePivotKeyName()
            : $query->getModel()->getQualifiedKeyName();
        $keys = (clone $query)->toBase()->pluck($this->getTable()->hasPivotRecordKeys() ? 'filament_pivot_key' : $qualifiedKeyName)->all();

        $this->constrainTableQueryToResolvedRecordKeys($query, $keys);
        $query->getQuery()->limit = null;
        $query->getQuery()->offset = null;

        // The ordering has already determined the selection. MySQL does not
        // support `ORDER BY` in joined deletes, even without a limit.
        return $query->reorder();
    }

    /**
     * @param  array<int | string>  $keys
     */
    protected function constrainTableQueryToResolvedRecordKeys(Builder $query, array $keys): void
    {
        $hasPivotRecordKeys = $this->getTable()->hasPivotRecordKeys();
        $qualifiedKeyName = $hasPivotRecordKeys
            ? $this->getQualifiedTablePivotKeyName()
            : $query->getModel()->getQualifiedKeyName();
        $connection = $query->getModel()->getConnection();

        if ($connection->getDriverName() === 'sqlsrv') {
            // SQL Server allows at most 2,100 parameters per statement. Escape
            // database-resolved keys as literals, preserving Unicode strings.
            $query->whereIn($qualifiedKeyName, array_map(
                static fn (int | string $key) => $connection->raw((is_string($key) ? 'N' : '') . $connection->escape($key)),
                $keys,
            ));

            return;
        }

        if ($hasPivotRecordKeys) {
            $query->whereIn($qualifiedKeyName, $keys);
        } else {
            $query->whereKey($keys);
        }
    }

    protected function constrainQueryToSelectableTableRecords(Builder $query): void
    {
        $table = $this->getTable();

        if (! $table->checksIfRecordIsSelectable()) {
            return;
        }

        $selectableKeys = [];

        $this->lazyTableRecordsByKey(clone $query, 1000)->each(function (Model $record) use (&$selectableKeys, $table): void {
            if ($table->isRecordSelectable($record)) {
                $selectableKeys[] = $table->hasPivotRecordKeys() ? $record->getAttribute('filament_pivot_key') : $record->getKey();
            }
        });

        if ($table->hasPivotRecordKeys() && $query->getQuery()->offset) {
            $query->getQuery()->offset = null;
        }

        $this->constrainTableQueryToResolvedRecordKeys($query, $selectableKeys);
    }

    protected function lazyTableRecordsByKey(Builder $query, int $chunkSize): LazyCollection
    {
        $hasPivotRecordKeys = $this->getTable()->hasPivotRecordKeys();

        if ($hasPivotRecordKeys) {
            $query = $this->applyPivotRecordKeyToTableQuery($query);
        }

        $query = $query->applyScopes()->withoutGlobalScopes();
        $qualifiedKeyName = $hasPivotRecordKeys ? $this->getQualifiedTablePivotKeyName() : $query->getModel()->getQualifiedKeyName();
        $keyName = $hasPivotRecordKeys ? 'filament_pivot_key' : $query->getModel()->getKeyName();
        $limit = $query->getQuery()->limit;

        if (($limit !== null) || $query->getQuery()->offset) {
            // Resolve bounded selection before changing the traversal order.
            $keys = (clone $query)->toBase()->pluck($hasPivotRecordKeys ? $keyName : $qualifiedKeyName)->all();
            $this->constrainTableQueryToResolvedRecordKeys($query, $keys);
            $query->getQuery()->limit = null;
            $query->getQuery()->offset = null;
        }

        $records = $query->reorder()->lazyById($chunkSize, $qualifiedKeyName, $keyName);

        return ($limit !== null) ? $records->take($limit) : $records;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    #[Unexposed]
    public function shouldSelectCurrentPageOnly(): bool
    {
        return false;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    #[Unexposed]
    public function shouldDeselectAllRecordsWhenTableFiltered(): bool
    {
        return true;
    }

    /**
     * @deprecated Use the `getMountedAction()` method instead.
     */
    #[Unexposed]
    public function getMountedTableBulkAction(): ?Action
    {
        return $this->getMountedAction();
    }

    /**
     * @deprecated Use the `getMountedActionSchema()` method instead.
     */
    #[Unexposed]
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
