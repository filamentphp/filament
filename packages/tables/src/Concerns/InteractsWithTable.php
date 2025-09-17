<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\WithPagination;

trait InteractsWithTable
{
    use CanBeStriped;
    use CanDeferLoading;
    use CanGroupRecords;
    use CanPaginateRecords;
    use CanPollRecords;
    use CanReorderRecords;
    use CanSearchRecords;
    use CanSortRecords;
    use CanSummarizeRecords;
    use HasActions;
    use HasBulkActions;
    use HasColumnManager;
    use HasColumns;
    use HasContent;
    use HasEmptyState;
    use HasFilters;
    use HasHeader;
    use HasRecordAction;
    use HasRecords;
    use WithPagination {
        WithPagination::resetPage as resetLivewirePage;
    }

    protected Table $table;

    protected bool $hasTableModalRendered = false;

    protected bool $shouldMountInteractsWithTable = false;

    public function bootedInteractsWithTable(): void
    {
        $this->table = $this->table($this->makeTable());

        $this->cacheSchema('tableFiltersForm', $this->getTableFiltersForm());

        $this->cacheMountedActions($this->mountedActions);

        $this->initTableColumnManager();

        if (! $this->shouldMountInteractsWithTable) {
            return;
        }

        $shouldPersistFiltersInSession = $this->getTable()->persistsFiltersInSession();
        $filtersSessionKey = $this->getTableFiltersSessionKey();

        if (! count($this->tableFilters ?? [])) {
            $this->tableFilters = null;
        }

        if (
            ($this->tableFilters === null) &&
            $shouldPersistFiltersInSession &&
            session()->has($filtersSessionKey)
        ) {
            $this->tableFilters = session()->get($filtersSessionKey) ?? [];
        }

        // https://github.com/filamentphp/filament/pull/7999
        if ($this->tableFilters) {
            $this->normalizeTableFilterValuesFromQueryString($this->tableFilters);
        }

        $this->getTableFiltersForm()->fill($this->tableFilters);

        if ($this->getTable()->hasDeferredFilters()) {
            $this->tableFilters = $this->tableDeferredFilters;
        }

        if ($shouldPersistFiltersInSession) {
            session()->put(
                $filtersSessionKey,
                $this->tableFilters,
            );
        }

        if ($this->getTable()->isDefaultGroupSelectable()) {
            $this->tableGrouping = "{$this->getTable()->getDefaultGroup()->getId()}:asc";
        }

        $shouldPersistSearchInSession = $this->getTable()->persistsSearchInSession();
        $searchSessionKey = $this->getTableSearchSessionKey();

        if (
            blank($this->tableSearch) &&
            $shouldPersistSearchInSession &&
            session()->has($searchSessionKey)
        ) {
            $this->tableSearch = session()->get($searchSessionKey);
        }

        $this->tableSearch = strval($this->tableSearch);

        if ($shouldPersistSearchInSession) {
            session()->put(
                $searchSessionKey,
                $this->tableSearch,
            );
        }

        $shouldPersistColumnSearchesInSession = $this->getTable()->persistsColumnSearchesInSession();
        $columnSearchesSessionKey = $this->getTableColumnSearchesSessionKey();

        if (
            (blank($this->tableColumnSearches) || ($this->tableColumnSearches === [])) &&
            $shouldPersistColumnSearchesInSession &&
            session()->has($columnSearchesSessionKey)
        ) {
            $this->tableColumnSearches = session()->get($columnSearchesSessionKey) ?? [];
        }

        $this->tableColumnSearches = $this->castTableColumnSearches(
            $this->tableColumnSearches ?? [],
        );

        if ($shouldPersistColumnSearchesInSession) {
            session()->put(
                $columnSearchesSessionKey,
                $this->tableColumnSearches,
            );
        }

        $shouldPersistSortInSession = $this->getTable()->persistsSortInSession();
        $sortSessionKey = $this->getTableSortSessionKey();

        if (
            blank($this->tableSort) &&
            $shouldPersistSortInSession &&
            session()->has($sortSessionKey)
        ) {
            $this->tableSort = session()->get($sortSessionKey);
        }

        if ($shouldPersistSortInSession) {
            session()->put(
                $sortSessionKey,
                $this->tableSort,
            );
        }

        if ($this->getTable()->isPaginated()) {
            $this->tableRecordsPerPage = $this->getDefaultTableRecordsPerPageSelectOption();
        }
    }

    public function mountInteractsWithTable(): void
    {
        $this->shouldMountInteractsWithTable = true;
    }

    public function table(Table $table): Table
    {
        return $table;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    protected function makeTable(): Table
    {
        return Table::make($this)
            ->query(fn (): Builder | Relation | null => $this->getTableQuery())
            ->when($this->getTableActions(), fn (Table $table, array $actions): Table => $table->actions($actions))
            ->when($this->getTableActionsColumnLabel(), fn (Table $table, string $actionsColumnLabel): Table => $table->actionsColumnLabel($actionsColumnLabel))
            ->when($this->getTableColumns(), fn (Table $table, array $columns): Table => $table->columns($columns))
            ->when(($columnManagerColumns = $this->getTableColumnToggleFormColumns()) !== 1, fn (Table $table): Table => $table->columnManagerColumns($columnManagerColumns))
            ->when($this->getTableColumnToggleFormMaxHeight(), fn (Table $table, string $columnManagerMaxHeight): Table => $table->columnManagerMaxHeight($columnManagerMaxHeight))
            ->when($this->getTableColumnToggleFormWidth(), fn (Table $table, string $columnManagerWidth): Table => $table->columnManagerWidth($columnManagerWidth))
            ->when($this->getTableContent(), fn (Table $table, View $content): Table => $table->content($content))
            ->when($this->getTableContentFooter(), fn (Table $table, View $contentFooter): Table => $table->contentFooter($contentFooter))
            ->when($this->getTableContentGrid(), fn (Table $table, array $contentGrid): Table => $table->contentGrid($contentGrid))
            ->when($this->getDefaultTableSortColumn(), fn (Table $table, string $defaultSortColumn): Table => $table->defaultSort($defaultSortColumn, $this->getDefaultTableSortDirection()))
            ->when($this->isTableLoadingDeferred(), fn (Table $table): Table => $table->deferLoading())
            ->when($this->getTableDescription(), fn (Table $table, string | Htmlable $description): Table => $table->description($description))
            ->when(! $this->shouldDeselectAllRecordsWhenTableFiltered(), fn (Table $table): Table => $table->deselectAllRecordsWhenFiltered(false))
            ->when($this->getTableEmptyState(), fn (Table $table, View $emptyState): Table => $table->emptyState($emptyState))
            ->when($this->getTableEmptyStateActions(), fn (Table $table, array $emptyStateActions): Table => $table->emptyStateActions($emptyStateActions))
            ->when($this->getTableEmptyStateDescription(), fn (Table $table, string $emptyStateDescription): Table => $table->emptyStateDescription($emptyStateDescription))
            ->when($this->getTableEmptyStateHeading(), fn (Table $table, string $emptyStateHeading): Table => $table->emptyStateHeading($emptyStateHeading))
            ->when($this->getTableEmptyStateIcon(), fn (Table $table, string $emptyStateIcon): Table => $table->emptyStateIcon($emptyStateIcon))
            ->when($this->getTableFilters(), fn (Table $table, array $filters): Table => $table->filters($filters))
            ->when($this->getTableFiltersFormMaxHeight(), fn (Table $table, string $filtersFormMaxHeight): Table => $table->filtersFormMaxHeight($filtersFormMaxHeight))
            ->when($this->getTableFiltersFormWidth(), fn (Table $table, string $filtersFormWidth): Table => $table->filtersFormWidth($filtersFormWidth))
            ->when($this->getTableBulkActions(), fn (Table $table, array $groupedBulkActions): Table => $table->groupedBulkActions($groupedBulkActions))
            ->when($this->getTableHeader(), fn (Table $table, View | Htmlable $header): Table => $table->header($header))
            ->when($this->getTableHeaderActions(), fn (Table $table, array $headerActions): Table => $table->headerActions($headerActions))
            ->when($this->getTableModelLabel(), fn (Table $table, string $modelLabel): Table => $table->modelLabel($modelLabel))
            ->when(! $this->isTablePaginationEnabled(), fn (Table $table): Table => $table->paginated(false))
            ->when($this->isTablePaginationEnabledWhileReordering(), fn (Table $table): Table => $table->paginatedWhileReordering())
            ->when($this->getTableRecordsPerPageSelectOptions(), fn (Table $table, array $paginationPageOptions): Table => $table->paginationPageOptions($paginationPageOptions))
            ->when($this->shouldPersistTableFiltersInSession(), fn (Table $table): Table => $table->persistFiltersInSession())
            ->when($this->shouldPersistTableSearchInSession(), fn (Table $table): Table => $table->persistSearchInSession())
            ->when($this->shouldPersistTableColumnSearchInSession(), fn (Table $table): Table => $table->persistColumnSearchesInSession())
            ->when($this->shouldPersistTableSortInSession(), fn (Table $table): Table => $table->persistSortInSession())
            ->when($this->getTablePluralModelLabel(), fn (Table $table, string $pluralModelLabel): Table => $table->pluralModelLabel($pluralModelLabel))
            ->when($this->getTablePollingInterval(), fn (Table $table, string $pollingInterval): Table => $table->poll($pollingInterval))
            ->when($this->getTableRecordAction(), fn (Table $table, string $recordAction): Table => $table->recordAction($recordAction))
            ->recordTitle(fn (Model $record): ?string => $this->getTableRecordTitle($record))
            ->when($this->getTableReorderColumn(), fn (Table $table, string $reorderColumn): Table => $table->reorderable($reorderColumn))
            ->when($this->shouldSelectCurrentPageOnly(), fn (Table $table): Table => $table->selectCurrentPageOnly())
            ->when($this->isTableStriped(), fn (Table $table): Table => $table->striped());
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return null;
    }

    public function getIdentifiedTableQueryStringPropertyNameFor(string $property): string
    {
        if (filled($identifier = $this->getTable()->getQueryStringIdentifier())) {
            return $identifier . ucfirst($property);
        }

        return $property;
    }

    public function getActiveTableLocale(): ?string
    {
        return null;
    }

    /**
     * @param  ?string  $pageName
     */
    public function resetPage($pageName = null): void
    {
        $this->resetLivewirePage($pageName ?? $this->getTablePaginationPageName());
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableQuery(): Builder | Relation | null
    {
        return null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function normalizeTableFilterValuesFromQueryString(array &$data): void
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $this->normalizeTableFilterValuesFromQueryString($value);
            } elseif ($value === 'null') {
                $value = null;
            } elseif ($value === 'false') {
                $value = false;
            } elseif ($value === 'true') {
                $value = true;
            }
        }
    }

    public function resetTable(): void
    {
        $this->bootedInteractsWithTable();

        $this->resetTableFiltersForm();

        $this->resetPage();

        $this->flushCachedTableRecords();
    }
}
