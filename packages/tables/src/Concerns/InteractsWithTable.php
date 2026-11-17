<?php

namespace Filament\Tables\Concerns;

use Filament\Facades\Filament;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\WithPagination;
use ReflectionMethod;

use function Filament\Support\encode_key_segments;

trait InteractsWithTable
{
    use CanBeLazyLoaded;
    use CanBeStriped;
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
        WithPagination::setPage as setLivewirePage;
    }

    protected Table $table;

    protected bool $hasTableModalRendered = false;

    protected bool $shouldMountInteractsWithTable = false;

    #[Unexposed]
    public function bootedInteractsWithTable(): void
    {
        $this->table = $this->table($this->makeTable());

        $this->cacheSchema('tableFiltersForm', $this->getTableFiltersForm(...));

        if (empty($this->cacheMountedActions($this->mountedActions))) {
            $this->mountedActions = [];
        }

        $this->initTableColumnManager();

        if (! $this->shouldMountInteractsWithTable) {
            return;
        }

        $this->initialTableRecordsPerPage ??= $this->tableRecordsPerPage ?? 10;

        $shouldPersistFilters = $this->getTable()->shouldPersistFilters();
        $filtersPersistenceKey = $this->getTableFiltersPersistenceKey();

        if (! count($this->tableFilters ?? [])) {
            $this->tableFilters = null;
        }

        if (
            ($this->tableFilters === null) &&
            $shouldPersistFilters
        ) {
            $this->tableFilters = $this->getTable()->getFiltersPersistenceStore()->get($filtersPersistenceKey);
        }

        // https://github.com/filamentphp/filament/pull/7999
        if ($this->tableFilters) {
            $this->normalizeTableFilterValuesFromQueryString($this->tableFilters);
        }

        $this->getTableFiltersForm()->fill($this->tableFilters);

        if ($this->getTable()->hasDeferredFilters()) {
            $this->tableFilters = $this->tableDeferredFilters;
        }

        if ($shouldPersistFilters) {
            $this->getTable()->getFiltersPersistenceStore()->put(
                $filtersPersistenceKey,
                $this->tableFilters,
            );
        }

        $shouldPersistGroup = $this->getTable()->shouldPersistGroup();
        $groupingPersistenceKey = $this->getTableGroupingPersistenceKey();
        $hasPersistedGroup = $shouldPersistGroup && $this->getTable()->getGroupPersistenceStore()->exists($groupingPersistenceKey);

        if (blank($this->tableGrouping)) {
            if ($hasPersistedGroup) {
                $persistedGrouping = $this->getTable()->getGroupPersistenceStore()->get($groupingPersistenceKey);
                $this->tableGrouping = is_string($persistedGrouping) ? $persistedGrouping : null;
            } elseif ($this->getTable()->isDefaultGroupSelectable()) {
                $this->tableGrouping = "{$this->getTable()->getDefaultGroup()->getId()}:{$this->getTable()->getDefaultGroupDirection()}";
            }
        }

        if ($shouldPersistGroup) {
            $this->getTable()->getGroupPersistenceStore()->put(
                $groupingPersistenceKey,
                $this->tableGrouping,
            );
        }

        $shouldPersistSearch = $this->getTable()->shouldPersistSearch();
        $searchPersistenceKey = $this->getTableSearchPersistenceKey();

        if (
            blank($this->tableSearch) &&
            $shouldPersistSearch &&
            (($persistedSearch = $this->getTable()->getSearchPersistenceStore()->get($searchPersistenceKey)) !== null)
        ) {
            $this->tableSearch = $persistedSearch;
        }

        $this->tableSearch = strval($this->tableSearch);

        if ($shouldPersistSearch) {
            $this->getTable()->getSearchPersistenceStore()->put(
                $searchPersistenceKey,
                $this->tableSearch,
            );
        }

        $shouldPersistColumnSearches = $this->getTable()->shouldPersistColumnSearches();
        $columnSearchesPersistenceKey = $this->getTableColumnSearchesPersistenceKey();

        if (
            (blank($this->tableColumnSearches) || ($this->tableColumnSearches === [])) &&
            $shouldPersistColumnSearches &&
            (($persistedColumnSearches = $this->getTable()->getColumnSearchesPersistenceStore()->get($columnSearchesPersistenceKey)) !== null)
        ) {
            $this->tableColumnSearches = $persistedColumnSearches;
        }

        $this->tableColumnSearches = $this->castTableColumnSearches(
            $this->tableColumnSearches,
        );

        // Seed individually searchable columns named after a JavaScript array property (e.g. `length`), so `$tableColumnSearches` serializes to a JSON object instead of an array and `wire:model` reads the search value rather than the array property.
        $this->fillReservedTableColumnSearchKeys();

        if ($shouldPersistColumnSearches) {
            $this->getTable()->getColumnSearchesPersistenceStore()->put(
                $columnSearchesPersistenceKey,
                $this->tableColumnSearches,
            );
        }

        $shouldPersistSort = $this->getTable()->shouldPersistSort();
        $sortPersistenceKey = $this->getTableSortPersistenceKey();

        if (
            blank($this->tableSort) &&
            $shouldPersistSort &&
            (($persistedSort = $this->getTable()->getSortPersistenceStore()->get($sortPersistenceKey)) !== null)
        ) {
            $this->tableSort = is_string($persistedSort) ? $persistedSort : null;
        }

        if ($shouldPersistSort) {
            $this->getTable()->getSortPersistenceStore()->put(
                $sortPersistenceKey,
                $this->tableSort,
            );
        }

        if ($this->getTable()->isPaginated()) {
            $this->tableRecordsPerPage ??= $this->getDefaultTableRecordsPerPageSelectOption();

            if ($this->getTable()->enforcesPaginationPageOptions()) {
                $this->tableRecordsPerPage = $this->getTableRecordsPerPage();
            }
        }
    }

    #[Unexposed]
    public function mountInteractsWithTable(): void
    {
        $this->shouldMountInteractsWithTable = true;
    }

    #[Unexposed]
    public function table(Table $table): Table
    {
        return $table;
    }

    #[Unexposed]
    public function getTable(): Table
    {
        return $this->table;
    }

    protected function makeTable(): Table
    {
        return Table::make($this)
            ->query(fn (): Builder | Relation | null => $this->getTableQuery())
            ->when($this->getTableActions(), fn (Table $table, array $actions): Table => $table->recordActions($actions))
            ->when($this->getTableActionsColumnLabel(), fn (Table $table, string $actionsColumnLabel): Table => $table->recordActionsColumnLabel($actionsColumnLabel))
            ->when($this->getTableColumns(), fn (Table $table, array $columns): Table => $table->columns($columns))
            ->when(($columnManagerColumns = $this->getTableColumnToggleFormColumns()) !== 1, fn (Table $table): Table => $table->columnManagerColumns($columnManagerColumns))
            ->when($this->getTableColumnToggleFormMaxHeight(), fn (Table $table, string $columnManagerMaxHeight): Table => $table->columnManagerMaxHeight($columnManagerMaxHeight))
            ->when($this->getTableColumnToggleFormWidth(), fn (Table $table, string $columnManagerWidth): Table => $table->columnManagerWidth($columnManagerWidth))
            ->when($this->getTableContent(), fn (Table $table, View $content): Table => $table->content($content))
            ->when($this->getTableContentFooter(), fn (Table $table, View $contentFooter): Table => $table->contentFooter($contentFooter))
            ->when($this->getTableContentGrid(), fn (Table $table, array $contentGrid): Table => $table->contentGrid($contentGrid))
            ->when($this->getDefaultTableSortColumn(), fn (Table $table, string $defaultSortColumn): Table => $table->defaultSort($defaultSortColumn, $this->getDefaultTableSortDirection()))
            ->when($this->isTableLoadingDeferred(), fn (Table $table): Table => $table->lazyLoaded())
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
            ->when(
                (new ReflectionMethod($this, 'getTableRecordTitle'))->getFileName() !== (new ReflectionMethod(HasRecords::class, 'getTableRecordTitle'))->getFileName(),
                fn (Table $table): Table => $table->recordTitle(fn (Model $record): ?string => $this->getTableRecordTitle($record)),
            )
            ->when($this->getTableReorderColumn(), fn (Table $table, string $reorderColumn): Table => $table->reorderable($reorderColumn))
            ->when($this->shouldSelectCurrentPageOnly(), fn (Table $table): Table => $table->selectCurrentPageOnly())
            ->when($this->isTableStriped(), fn (Table $table): Table => $table->striped());
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return null;
    }

    #[Unexposed]
    public function getIdentifiedTableQueryStringPropertyNameFor(string $property): string
    {
        if (filled($identifier = $this->getTable()->getQueryStringIdentifier())) {
            return $identifier . ucfirst($property);
        }

        return $property;
    }

    #[Unexposed]
    public function getTableSessionKey(string $key, bool $isTenantScoped = false): string
    {
        return $this->getTablePersistenceKey($key, $isTenantScoped);
    }

    #[Unexposed]
    public function getTablePersistenceKey(string $key, bool $isTenantScoped = false): string
    {
        $namespace = $this::class;

        if ($isTenantScoped && class_exists(Filament::class) && filled($tenantKey = Filament::getTenant()?->getKey())) {
            $namespace = encode_key_segments($namespace, (string) $tenantKey);
        }

        $table = hash('sha256', $namespace);

        if (filled($identifier = $this->getTable()->getIdentifier())) {
            $table .= '.' . hash('sha256', $identifier);
        }

        return "filament.tables.{$table}.{$key}";
    }

    #[Unexposed]
    public function getActiveTableLocale(): ?string
    {
        return null;
    }

    #[Unexposed]
    public function resetPage(?string $pageName = null): void
    {
        $this->resetLivewirePage($pageName ?? $this->getTablePaginationPageName());
    }

    #[Exposed]
    public function setPage(int | string $page, ?string $pageName = null): void
    {
        $defaultPageName = $this->getTablePaginationPageName();

        $pageName ??= $defaultPageName;

        $this->setLivewirePage($page, $pageName);

        if (($pageName === $defaultPageName) && $this->getTable()->shouldScrollToTopOnPageChange()) {
            $this->dispatch('scrollToTopOfTable')->self();
        }
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

    #[Unexposed]
    public function resetTable(): void
    {
        $this->bootedInteractsWithTable();

        $this->resetTableFiltersForm();

        $this->resetPage();

        $this->flushCachedTableRecords();
    }
}
