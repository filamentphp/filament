<?php

namespace Filament\Tables\Concerns;

use Exception;
use Filament\Forms;
use Filament\Tables\Contracts\HasRelationshipTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;
use function Livewire\invade;

trait InteractsWithTable
{
    use CanBeStriped;
    use CanPaginateRecords;
    use CanPollRecords;
    use CanReorderRecords;
    use CanSearchRecords;
    use CanSelectRecords;
    use CanSortRecords;
    use CanToggleColumns;
    use CanDeferLoading;
    use HasActions;
    use HasBulkActions;
    use HasColumns;
    use HasContent;
    use HasEmptyState;
    use HasFilters;
    use HasHeader;
    use HasRecords;
    use HasRecordAction;
    use HasRecordClasses;
    use HasRecordUrl;
    use Forms\Concerns\InteractsWithForms;

    protected bool $hasMounted = false;

    protected Table $table;

    public function bootedInteractsWithTable(): void
    {
        $this->table = $this->getTable();

        $this->cacheTableActions();
        $this->cacheTableBulkActions();
        $this->cacheTableEmptyStateActions();
        $this->cacheTableHeaderActions();

        $this->cacheTableColumns();
        $this->cacheTableColumnActions();
        $this->cacheForm('toggleTableColumnForm', $this->getTableColumnToggleForm());

        $this->cacheTableFilters();
        $this->cacheForm('tableFiltersForm', $this->getTableFiltersForm());

        if ($this->hasMounted) {
            return;
        }

        if (blank($this->toggledTableColumns) || ($this->toggledTableColumns === [])) {
            $this->getTableColumnToggleForm()->fill(session()->get(
                $this->getTableColumnToggleFormStateSessionKey(),
                $this->getDefaultTableColumnToggleState()
            ));
        }

        if (! count($this->tableFilters ?? [])) {
            $this->tableFilters = null;
        }

        $shouldPersistTableFiltersInSession = $this->shouldPersistTableFiltersInSession();
        $filtersSessionKey = $this->getTableFiltersSessionKey();

        if (($this->tableFilters === null) && $shouldPersistTableFiltersInSession && session()->has($filtersSessionKey)) {
            $this->tableFilters = array_merge(
                $this->tableFilters ?? [],
                session()->get($filtersSessionKey) ?? [],
            );
        }

        $this->getTableFiltersForm()->fill($this->tableFilters);

        if ($shouldPersistTableFiltersInSession) {
            session()->put(
                $filtersSessionKey,
                $this->tableFilters,
            );
        }

        $shouldPersistTableSearchInSession = $this->shouldPersistTableSearchInSession();
        $searchSessionKey = $this->getTableSearchSessionKey();

        if (blank($this->tableSearchQuery) && $shouldPersistTableSearchInSession && session()->has($searchSessionKey)) {
            $this->tableSearchQuery = session()->get($searchSessionKey);
        }

        $this->tableSearchQuery = strval($this->tableSearchQuery);

        if ($shouldPersistTableSearchInSession) {
            session()->put(
                $searchSessionKey,
                $this->tableSearchQuery,
            );
        }

        $shouldPersistTableColumnSearchInSession = $this->shouldPersistTableColumnSearchInSession();
        $columnSearchSessionKey = $this->getTableColumnSearchSessionKey();

        if ((blank($this->tableColumnSearchQueries) || ($this->tableColumnSearchQueries === [])) && $shouldPersistTableColumnSearchInSession && session()->has($columnSearchSessionKey)) {
            $this->tableColumnSearchQueries = session()->get($columnSearchSessionKey);
        }

        $this->tableColumnSearchQueries = $this->castTableColumnSearchQueries(
            $this->tableColumnSearchQueries ?? [],
        );

        if ($shouldPersistTableColumnSearchInSession) {
            session()->put(
                $columnSearchSessionKey,
                $this->tableColumnSearchQueries,
            );
        }

        $shouldPersistTableSortInSession = $this->shouldPersistTableSortInSession();
        $sortSessionKey = $this->getTableSortSessionKey();

        if (blank($this->tableSortColumn) && $shouldPersistTableSortInSession && session()->has($sortSessionKey)) {
            $sort = session()->get($sortSessionKey);

            $this->tableSortColumn = $sort['column'] ?? null;
            $this->tableSortDirection = $sort['direction'] ?? null;
        }

        if ($shouldPersistTableSortInSession) {
            session()->put(
                $sortSessionKey,
                [
                    'column' => $this->tableSortColumn,
                    'direction' => $this->tableSortDirection,
                ],
            );
        }

        $this->hasMounted = true;
    }

    public function mountInteractsWithTable(): void
    {
        if ($this->isTablePaginationEnabled()) {
            $this->tableRecordsPerPage = $this->getDefaultTableRecordsPerPageSelectOption();
        }
    }

    protected function getCachedTable(): Table
    {
        return $this->table;
    }

    protected function getTable(): Table
    {
        return Table::make($this);
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return null;
    }

    protected function getIdentifiedTableQueryStringPropertyNameFor(string $property): string
    {
        if (filled($this->getTableQueryStringIdentifier())) {
            return $this->getTableQueryStringIdentifier() . ucfirst($property);
        }

        return $property;
    }

    protected function getInteractsWithTableForms(): array
    {
        return $this->getTableForms();
    }

    public function getActiveTableLocale(): ?string
    {
        return null;
    }

    protected function getTableForms(): array
    {
        return [
            'mountedTableActionForm' => $this->getMountedTableActionForm(),
            'mountedTableBulkActionForm' => $this->getMountedTableBulkActionForm(),
        ];
    }

    protected function getTableQuery(): Builder | Relation
    {
        if (! $this instanceof HasRelationshipTable) {
            $livewireClass = static::class;

            throw new Exception("Class [{$livewireClass}] must define a [getTableQuery()] method.");
        }

        $relationship = $this->getRelationship();

        $query = $relationship->getQuery();

        if ($relationship instanceof HasManyThrough) {
            // https://github.com/laravel/framework/issues/4962
            $query->select($query->getModel()->getTable() . '.*');

            return $query;
        }

        if ($relationship instanceof BelongsToMany) {
            // https://github.com/laravel/framework/issues/4962
            if (! $this->allowsDuplicates()) {
                $this->selectPivotDataInQuery($query);
            }

            // https://github.com/filamentphp/filament/issues/2079
            $query->withCasts(
                app($relationship->getPivotClass())->getCasts(),
            );
        }

        return $query;
    }

    protected function selectPivotDataInQuery(Builder | Relation $query): Builder | Relation
    {
        if (! $this instanceof HasRelationshipTable) {
            return $query;
        }

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $columns = [
            $relationship->getTable() . '.*',
            $query->getModel()->getTable() . '.*',
        ];

        if (! $this->allowsDuplicates()) {
            $columns = array_merge(invade($relationship)->aliasedPivotColumns(), $columns);
        }

        $query->select($columns);

        return $query;
    }
}
