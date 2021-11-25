<?php

namespace Filament\Tables\Concerns;

use Filament\Forms;
use Filament\Tables\Table;
use Livewire\WithPagination;

trait InteractsWithTable
{
    use CanPaginateRecords;
    use CanSearchRecords;
    use CanSelectRecords;
    use CanSortRecords;
    use HasActions;
    use HasBulkActions;
    use HasColumns;
    use HasContentFooter;
    use HasEmptyState;
    use HasFilters;
    use HasHeader;
    use HasRecords;
    use Forms\Concerns\InteractsWithForms;
    use WithPagination;

    protected Table $table;

    public function bootedInteractsWithTable(): void
    {
        $this->table = $this->getTable();

        $this->cacheTableActions();

        $this->cacheTableBulkActions();

        $this->cacheTableColumns();

        $this->cacheTableFilters();

        if ($this->tableFilters === null) {
            $this->getTableFiltersForm()->fill();
        }
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
            ->emptyStateActions($this->getTableEmptyStateActions())
            ->emptyStateDescription($this->getTableEmptyStateDescription())
            ->emptyStateHeading($this->getTableEmptyStateHeading())
            ->emptyStateIcon($this->getTableEmptyStateIcon())
            ->enablePagination($this->isTablePaginationEnabled())
            ->header($this->getTableHeader())
            ->headerActions($this->getTableHeaderActions())
            ->heading($this->getTableHeading())
            ->recordsPerPageSelectOptions($this->getTableRecordsPerPageSelectOptions());
    }

    protected function getForms(): array
    {
        return $this->getTableForms();
    }

    protected function getTableForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->model($this->getFormModel()),
            'mountedTableActionForm' => $this->makeForm()
                ->statePath('mountedTableActionData'),
            'mountedTableBulkActionForm' => $this->makeForm()
                ->statePath('mountedTableBulkActionData'),
            'tableFiltersForm' => $this->makeForm()
                ->schema($this->getTableFiltersFormSchema())
                ->statePath('tableFilters')
                ->reactive(),
        ];
    }

    protected function makeTable(): Table
    {
        return Table::make($this);
    }
}
