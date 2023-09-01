<?php

namespace Filament\Tables\Concerns;

use Closure;
use Filament\Forms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\WithPagination;

trait InteractsWithTable
{
    use CanDeferLoading;
    use CanGroupRecords;
    use CanPaginateRecords;
    use CanReorderRecords;
    use CanSearchRecords;
    use CanSortRecords;
    use CanSummarizeRecords;
    use CanToggleColumns;
    use HasActions;
    use HasBulkActions;
    use HasColumns;
    use HasFilters;
    use HasRecords;
    use WithPagination {
        WithPagination::resetPage as resetLivewirePage;
    }
    use CanBeStriped;
    use CanPollRecords;
    use HasContent;
    use HasEmptyState;
    use HasHeader;
    use HasRecordAction;
    use HasRecordClasses;
    use HasRecordUrl;

    protected Table $table;

    protected bool $hasTableModalRendered = false;

    protected bool $shouldMountInteractsWithTable = false;

    public function bootedInteractsWithTable(): void
    {
        $this->table = Action::configureUsing(
            Closure::fromCallable([$this, 'configureTableAction']),
            fn (): Table => BulkAction::configureUsing(
                Closure::fromCallable([$this, 'configureTableBulkAction']),
                fn (): Table => $this->table($this->makeTable()),
            ),
        );

        $this->cacheForm('toggleTableColumnForm', $this->getTableColumnToggleForm());

        $this->cacheForm('tableFiltersForm', $this->getTableFiltersForm());

        if (! $this->shouldMountInteractsWithTable) {
            return;
        }

        if (! count($this->toggledTableColumns ?? [])) {
            $this->getTableColumnToggleForm()->fill(session()->get(
                $this->getTableColumnToggleFormStateSessionKey(),
                $this->getDefaultTableColumnToggleState()
            ));
        }

        $shouldPersistFiltersInSession = $this->getTable()->persistsFiltersInSession();
        $filtersSessionKey = $this->getTableFiltersSessionKey();

        if (! count($this->tableFilters ?? [])) {
            $this->tableFilters = null;
        }

        if (($this->tableFilters === null) && $shouldPersistFiltersInSession && session()->has($filtersSessionKey)) {
            $this->tableFilters = [
                ...($this->tableFilters ?? []),
                ...(session()->get($filtersSessionKey) ?? []),
            ];
        }

        // https://github.com/filamentphp/filament/pull/7999
        if ($this->tableFilters) {
            $this->normalizeTableFilterValuesFromQueryString($this->tableFilters);
        }

        $this->getTableFiltersForm()->fill($this->tableFilters);

        if ($shouldPersistFiltersInSession) {
            session()->put(
                $filtersSessionKey,
                $this->tableFilters,
            );
        }

        if ($this->getTable()->isDefaultGroupSelectable()) {
            $this->tableGrouping = $this->getTable()->getDefaultGroup()->getId();
        }

        $shouldPersistSearchInSession = $this->getTable()->persistsSearchInSession();
        $searchSessionKey = $this->getTableSearchSessionKey();

        if (blank($this->tableSearch) && $shouldPersistSearchInSession && session()->has($searchSessionKey)) {
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

        if ((blank($this->tableColumnSearches) || ($this->tableColumnSearches === [])) && $shouldPersistColumnSearchesInSession && session()->has($columnSearchesSessionKey)) {
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

        if (blank($this->tableSortColumn) && $shouldPersistSortInSession && session()->has($sortSessionKey)) {
            $sort = session()->get($sortSessionKey);

            $this->tableSortColumn = $sort['column'] ?? null;
            $this->tableSortDirection = $sort['direction'] ?? null;
        }

        if ($shouldPersistSortInSession) {
            session()->put(
                $sortSessionKey,
                [
                    'column' => $this->tableSortColumn,
                    'direction' => $this->tableSortDirection,
                ],
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
        return $table
            ->query($this->getTableQuery())
            ->actions($this->getTableActions())
            ->actionsColumnLabel($this->getTableActionsColumnLabel())
            ->groupedBulkActions($this->getTableBulkActions())
            ->checkIfRecordIsSelectableUsing($this->isTableRecordSelectable())
            ->columns($this->getTableColumns())
            ->columnToggleFormColumns($this->getTableColumnToggleFormColumns())
            ->columnToggleFormMaxHeight($this->getTableColumnToggleFormMaxHeight())
            ->columnToggleFormWidth($this->getTableColumnToggleFormWidth())
            ->content($this->getTableContent())
            ->contentFooter($this->getTableContentFooter())
            ->contentGrid($this->getTableContentGrid())
            ->defaultSort($this->getDefaultTableSortColumn(), $this->getDefaultTableSortDirection())
            ->deferLoading($this->isTableLoadingDeferred())
            ->description($this->getTableDescription())
            ->deselectAllRecordsWhenFiltered($this->shouldDeselectAllRecordsWhenTableFiltered())
            ->emptyState($this->getTableEmptyState())
            ->emptyStateActions($this->getTableEmptyStateActions())
            ->emptyStateDescription($this->getTableEmptyStateDescription())
            ->emptyStateHeading($this->getTableEmptyStateHeading())
            ->emptyStateIcon($this->getTableEmptyStateIcon())
            ->filters($this->getTableFilters())
            ->filtersFormMaxHeight($this->getTableFiltersFormMaxHeight())
            ->filtersFormWidth($this->getTableFiltersFormWidth())
            ->header($this->getTableHeader())
            ->headerActions($this->getTableHeaderActions())
            ->modelLabel($this->getTableModelLabel())
            ->paginated($this->isTablePaginationEnabled())
            ->paginatedWhileReordering($this->isTablePaginationEnabledWhileReordering())
            ->paginationPageOptions($this->getTableRecordsPerPageSelectOptions())
            ->persistFiltersInSession($this->shouldPersistTableFiltersInSession())
            ->persistSearchInSession($this->shouldPersistTableSearchInSession())
            ->persistColumnSearchesInSession($this->shouldPersistTableColumnSearchInSession())
            ->persistSortInSession($this->shouldPersistTableSortInSession())
            ->pluralModelLabel($this->getTablePluralModelLabel())
            ->poll($this->getTablePollingInterval())
            ->recordAction($this->getTableRecordActionUsing())
            ->recordClasses($this->getTableRecordClassesUsing())
            ->recordTitle(fn (Model $record): ?string => $this->getTableRecordTitle($record))
            ->recordUrl($this->getTableRecordUrlUsing())
            ->reorderable($this->getTableReorderColumn())
            ->selectCurrentPageOnly($this->shouldSelectCurrentPageOnly())
            ->striped($this->isTableStriped());
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    protected function makeTable(): Table
    {
        return Table::make($this);
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

    /**
     * @return array<string, Forms\Form>
     */
    protected function getInteractsWithTableForms(): array
    {
        return [
            'mountedTableActionForm' => $this->getMountedTableActionForm(),
            'mountedTableBulkActionForm' => $this->getMountedTableBulkActionForm(),
        ];
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
}
