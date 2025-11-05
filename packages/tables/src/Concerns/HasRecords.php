<?php

namespace Filament\Tables\Concerns;

use Filament\Support\ArrayRecord;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use LogicException;

use function Livewire\invade;

trait HasRecords
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected bool $allowsDuplicates = false;

    protected Collection | Paginator | CursorPaginator | null $cachedTableRecords = null;

    /**
     * @var array<string, Model | array<string, mixed>>
     */
    protected array $cachedTableRecordLookup = [];

    public function getFilteredTableQuery(): ?Builder
    {
        $query = $this->getTable()->getQuery();

        if (! $query) {
            return null;
        }

        return $this->filterTableQuery($query);
    }

    public function filterTableQuery(Builder $query): Builder
    {
        $this->applyFiltersToTableQuery($query);

        $this->applySearchToTableQuery($query);

        foreach ($this->getTable()->getVisibleColumns() as $column) {
            $column->applyRelationshipAggregates($query);

            if ($this->getTable()->isGroupsOnly()) {
                continue;
            }

            $column->applyEagerLoading($query);
        }

        return $query;
    }

    public function getFilteredSortedTableQuery(): ?Builder
    {
        $query = $this->getFilteredTableQuery();

        if (! $query) {
            return null;
        }

        $this->applyGroupingToTableQuery($query);

        $this->applySortingToTableQuery($query);

        return $query;
    }

    public function getTableQueryForExport(): Builder
    {
        $query = $this->getTable()->getQuery();

        $this->applyFiltersToTableQuery($query);
        $this->applySearchToTableQuery($query);
        $this->applySortingToTableQuery($query);

        return $query;
    }

    protected function hydratePivotRelationForTableRecords(EloquentCollection | Paginator | CursorPaginator $records): EloquentCollection | Paginator | CursorPaginator
    {
        $table = $this->getTable();
        $relationship = $table->getRelationship();

        if ($table->getRelationship() instanceof BelongsToMany && ! $table->allowsDuplicates()) {
            invade($relationship)->hydratePivotRelation($records->all());
        }

        return $records;
    }

    /**
     * @return array{0: Collection, 1: array<string, Model | array<string, mixed>>}
     */
    protected function prepareTableRecordsCollection(Collection $records): array
    {
        $records = $records->values();
        $table = $this->getTable();

        if (! $table->hasTree()) {
            $collection = $records->mapWithKeys(function (array | Model $record, string | int $key): array {
                if ($record instanceof Model) {
                    return [$record->getKey() => $record];
                }

                $keyName = ArrayRecord::getKeyName();

                $record[$keyName] ??= $key;
                $record[$keyName] = (string) $record[$keyName];

                return [$record[$keyName] => $record];
            });

            return [$collection, $collection->all()];
        }

        return $this->prepareTreeRecordsCollection($records);
    }

    /**
     * @return array{0: Collection, 1: array<string, Model | array<string, mixed>>}
     */
    protected function prepareTreeRecordsCollection(Collection $records, int $depth = 0, ?string $parentKey = null): array
    {
        $table = $this->getTable();
        $tree = $table->getTree();

        $childrenRelationship = $tree?->getChildrenRelationship() ?? 'children';
        $keyName = ArrayRecord::getKeyName();

        $prepared = collect();
        $lookup = [];

        foreach ($records as $key => $record) {
            if ($record instanceof Model) {
                $recordKey = $this->getTableRecordKey($record);
                $children = $this->extractModelChildrenForTree($record, $childrenRelationship);
            } else {
                if (! is_array($record)) {
                    $record = (array) $record;
                }

                $record[$keyName] ??= $key;
                $record[$keyName] = (string) $record[$keyName];
                $recordKey = $record[$keyName];

                $children = $this->extractArrayChildrenForTree($record, $childrenRelationship);
            }

            $childrenCollection = $children instanceof Collection
                ? $children
                : collect($children ?? []);

            if ($childrenCollection->isNotEmpty()) {
                [$childCollection, $childLookup] = $this->prepareTreeRecordsCollection($childrenCollection, $depth + 1, $recordKey);
            } else {
                $childCollection = collect();
                $childLookup = [];
            }

            $childCollection = $childCollection->values();

            if ($record instanceof Model) {
                $record->setRelation($childrenRelationship, $childCollection);
                $record->setAttribute('__filament_tree_parent', $parentKey);
                $record->setAttribute('__filament_tree_depth', $depth);
            } else {
                $record[$childrenRelationship] = $childCollection;
                $record['__filament_tree_parent'] = $parentKey;
                $record['__filament_tree_depth'] = $depth;
            }

            $prepared[$recordKey] = $record;
            $lookup[$recordKey] = $record;
            $lookup += $childLookup;
        }

        return [$prepared, $lookup];
    }

    protected function extractModelChildrenForTree(Model $record, string $relationship): Collection
    {
        if ($record->relationLoaded($relationship)) {
            $children = $record->getRelation($relationship);

            return $children instanceof Collection ? $children : collect($children);
        }

        $attribute = $record->getAttribute($relationship);

        if ($attribute instanceof Collection) {
            return $attribute;
        }

        if (is_array($attribute)) {
            return collect($attribute);
        }

        return collect();
    }

    /**
     * @param  array<string, mixed>  $record
     */
    protected function extractArrayChildrenForTree(array &$record, string $relationship): Collection
    {
        $children = $record[$relationship] ?? ($relationship === 'children'
            ? ($record['children'] ?? [])
            : ($record['children'] ?? []));

        unset($record[$relationship]);

        if ($relationship !== 'children') {
            unset($record['children']);
        }

        if ($children instanceof Collection) {
            return $children;
        }

        if (is_array($children)) {
            return collect($children);
        }

        return collect();
    }

    public function getTableRecords(): Collection | Paginator | CursorPaginator
    {
        if (! $this->getTable()->hasQuery()) {
            if ($this->cachedTableRecords) {
                return $this->cachedTableRecords;
            }

            $records = $this->getTable()->evaluate($this->getTable()->getDataSource(), [
                'columnSearches' => fn (): array => $this->getTableColumnSearches(),
                'filters' => fn (): ?array => $this->tableFilters,
                'page' => fn (): int | string => $this->getTablePage(),
                'recordsPerPage' => fn (): int | string => $this->getTableRecordsPerPage(),
                'search' => fn (): ?string => $this->getTableSearch(),
                'sort' => fn (): array => [$this->getTableSortColumn(), $this->getTableSortDirection()],
                'sortColumn' => fn (): ?string => $this->getTableSortColumn(),
                'sortDirection' => fn (): ?string => $this->getTableSortDirection(),
            ]);

            if (is_array($records)) {
                $collection = collect($records);
            } elseif (
                ($records instanceof Paginator || $records instanceof CursorPaginator) &&
                method_exists($records, 'getCollection')
            ) {
                $collection = $records->getCollection();
            } elseif ($records instanceof Collection) {
                $collection = $records;
            } else {
                $collection = collect($records);
            }

            [$collection, $lookup] = $this->prepareTableRecordsCollection($collection instanceof Collection ? $collection : collect($collection));

            $this->cachedTableRecordLookup = $lookup;

            if (
                ($records instanceof Paginator || $records instanceof CursorPaginator) &&
                method_exists($records, 'setCollection')
            ) {
                $records->setCollection($collection);
            } else {
                $records = $collection;
            }

            return $this->cachedTableRecords = $records;
        }

        if ($translatableContentDriver = $this->makeFilamentTranslatableContentDriver()) {
            $setRecordLocales = function (EloquentCollection | Paginator | CursorPaginator $records) use ($translatableContentDriver): EloquentCollection | Paginator | CursorPaginator {
                $records->transform(fn (Model $record) => $translatableContentDriver->setRecordLocale($record));

                return $records;
            };
        } else {
            $setRecordLocales = fn (EloquentCollection | Paginator | CursorPaginator $records): EloquentCollection | Paginator | CursorPaginator => $records;
        }

        if ($this->cachedTableRecords) {
            return $setRecordLocales($this->cachedTableRecords);
        }

        $query = $this->getFilteredSortedTableQuery();

        if (! $query) {
            $livewireClass = $this::class;

            throw new LogicException("Table [{$livewireClass}] must have a [query()], [relationship()], or [records()].");
        }

        if (
            (! $this->getTable()->isPaginated()) ||
            ($this->isTableReordering() && (! $this->getTable()->isPaginatedWhileReordering()))
        ) {
            return $setRecordLocales($this->cachedTableRecords = $this->hydratePivotRelationForTableRecords($query->get()));
        }

        return $setRecordLocales($this->cachedTableRecords = $this->hydratePivotRelationForTableRecords($this->paginateTableQuery($query)));
    }

    /**
     * @return Model | array<string, mixed> | null
     */
    protected function resolveTableRecord(?string $key): Model | array | null
    {
        if ($key === null) {
            return null;
        }

        if (! $this->getTable()->hasQuery()) {
            return $this->cachedTableRecordLookup[$key] ?? null;
        }

        if (! ($this->getTable()->getRelationship() instanceof BelongsToMany)) {
            return $this->getFilteredTableQuery()->find($key);
        }

        /** @var BelongsToMany $relationship */
        $relationship = $this->getTable()->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        $table = $this->getTable();

        $this->applyFiltersToTableQuery($relationship->getQuery());

        $query = $table->allowsDuplicates() ?
            $relationship->wherePivot($pivotKeyName, $key) :
            $relationship->where($relationship->getQualifiedRelatedKeyName(), $key);

        $record = $table->selectPivotDataInQuery($query)->first();

        return $record?->setRawAttributes($record->getRawOriginal());
    }

    /**
     * @return Model | array<string, mixed> | null
     */
    public function getTableRecord(?string $key): Model | array | null
    {
        $record = $this->resolveTableRecord($key);

        if ($record && filled($this->getActiveTableLocale())) {
            $this->makeFilamentTranslatableContentDriver()->setRecordLocale($record);
        }

        return $record;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function getTableRecordKey(Model | array $record): string
    {
        if (is_array($record)) {
            return $record[ArrayRecord::getKeyName()] ?? throw new LogicException('Record arrays must have a unique [key] entry for identification.');
        }

        $table = $this->getTable();

        if (! ($table->getRelationship() instanceof BelongsToMany && $table->allowsDuplicates())) {
            return $record->getKey();
        }

        /** @var BelongsToMany $relationship */
        $relationship = $table->getRelationship();

        $pivotClass = $relationship->getPivotClass();
        $pivotKeyName = app($pivotClass)->getKeyName();

        return $record->getAttributeValue($pivotKeyName);
    }

    public function getAllTableRecordsCount(): int
    {
        if ($this->cachedTableRecords instanceof LengthAwarePaginator) {
            return $this->cachedTableRecords->total();
        }

        return $this->getFilteredTableQuery()->count();
    }

    public function flushCachedTableRecords(): void
    {
        $this->cachedTableRecords = null;
        $this->cachedTableRecordLookup = [];
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function allowsDuplicates(): bool
    {
        return $this->allowsDuplicates;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function getTableRecordTitle(Model $record): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function getTableModelLabel(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    public function getTablePluralModelLabel(): ?string
    {
        return null;
    }
}
