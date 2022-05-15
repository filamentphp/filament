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
    use HasContent;
    use HasEmptyState;
    use HasFilters;
    use HasHeader;
    use HasRecords;
    use HasRecordAction;
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
        $this->cacheForm('toggleTableColumnForm', $this->getTableColumnToggleForm());

        $this->cacheTableFilters();
        $this->cacheForm('tableFiltersForm', $this->getTableFiltersForm());

        if (! $this->hasMounted) {
            $this->getTableColumnToggleForm()->fill(session()->get(
                $this->getTableColumnToggleFormStateSessionKey(),
                $this->getDefaultTableColumnToggleState()
            ));

            $this->getTableFiltersForm()->fill($this->tableFilters);

            $this->hasMounted = true;
        }
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
            ->content($this->getTableContent())
            ->contentFooter($this->getTableContentFooter())
            ->description($this->getTableDescription())
            ->emptyState($this->getTableEmptyState())
            ->emptyStateDescription($this->getTableEmptyStateDescription())
            ->emptyStateHeading($this->getTableEmptyStateHeading())
            ->emptyStateIcon($this->getTableEmptyStateIcon())
            ->enablePagination($this->isTablePaginationEnabled())
            ->filtersFormWidth($this->getTableFiltersFormWidth())
            ->filtersLayout($this->getTableFiltersLayout())
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

    protected function makeTable(): Table
    {
        return Table::make($this);
    }
}
