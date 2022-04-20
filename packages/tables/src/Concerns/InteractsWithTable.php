<?php

namespace Filament\Tables\Concerns;

use Filament\Forms;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method Builder getTableQuery()
 */
trait InteractsWithTable
{
    use CanPaginateRecords;
    use CanSearchRecords;
    use CanSelectRecords;
    use CanSortRecords;
    use CanToggleColumns;
    use HasActions;
    use HasBulkActions;
    use HasColumns;
    use HasContentFooter;
    use HasEmptyState;
    use HasFilters;
    use HasHeader;
    use HasRecords;
    use HasRecordAction;
    use HasRecordUrl;
    use Forms\Concerns\InteractsWithForms;

    protected Table $table;

    public function bootedInteractsWithTable(): void
    {
        $this->table = $this->getTable();

        $this->cacheTableActions();
        $this->cacheTableBulkActions();
        $this->cacheTableEmptyStateActions();
        $this->cacheTableHeaderActions();

        $this->cacheTableColumns();

        $this->cacheTableFilters();
        $this->getTableFiltersForm()->fill($this->tableFilters);

        $this->prepareToggledTableColumns();
    }

    public function mountInteractsWithTable(): void
    {
        if ($this->isTablePaginationEnabled()) {
            $this->tableRecordsPerPage = $this->getDefaultTableRecordsPerPageSelectOption();
        }

        $this->tableSortColumn ??= $this->getDefaultTableSortColumn();
        $this->tableSortDirection ??= $this->getDefaultTableSortDirection();
    }

    protected function getCachedTable(): Table
    {
        return $this->table;
    }

    protected function getTable(): Table
    {
        return $this->makeTable()
            ->contentFooter($this->getTableContentFooter())
            ->description($this->getTableDescription())
            ->emptyState($this->getTableEmptyState())
            ->emptyStateDescription($this->getTableEmptyStateDescription())
            ->emptyStateHeading($this->getTableEmptyStateHeading())
            ->emptyStateIcon($this->getTableEmptyStateIcon())
            ->enablePagination($this->isTablePaginationEnabled())
            ->filtersFormWidth($this->getTableFiltersFormWidth())
            ->recordAction($this->getTableRecordAction())
            ->getRecordUrlUsing($this->getTableRecordUrlUsing())
            ->header($this->getTableHeader())
            ->heading($this->getTableHeading())
            ->model($this->getTableQuery()->getModel()::class)
            ->recordsPerPageSelectOptions($this->getTableRecordsPerPageSelectOptions());
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

    protected function getTableForms(): array
    {
        return [
            'mountedTableActionForm' => $this->makeForm()
                ->schema(($action = $this->getMountedTableAction()) ? $action->getFormSchema() : [])
                ->model($this->getMountedTableActionRecord() ?? $this->getTableQuery()->getModel()::class)
                ->statePath('mountedTableActionData'),
            'mountedTableBulkActionForm' => $this->makeForm()
                ->schema(($action = $this->getMountedTableBulkAction()) ? $action->getFormSchema() : [])
                ->model($this->getTableQuery()->getModel()::class)
                ->statePath('mountedTableBulkActionData'),
            'tableFiltersForm' => $this->makeForm()
                ->schema($this->getTableFiltersFormSchema())
                ->columns($this->getTableFiltersFormColumns())
                ->statePath('tableFilters')
                ->reactive(),
            'toggleTableColumnForm' => $this->makeForm()
                ->schema($this->getTableColumnToggleFormSchema())
                ->columns($this->getTableColumnToggleFormColumns())
                ->statePath('toggledTableColumns')
                ->reactive(),
        ];
    }

    protected function makeTable(): Table
    {
        return Table::make($this);
    }
}
