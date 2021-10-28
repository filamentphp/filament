<?php

namespace Filament\Tables\Concerns;

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
    use HasEmptyState;
    use HasFilters;
    use HasRecords;
    use WithPagination;

    protected Table $table;

    public function bootInteractsWithTable(): void
    {
        $this->table = $this->getTable();

        $this->cacheTableActions();

        $this->cacheTableBulkActions();

        $this->cacheTableColumns();

        $this->cacheTableFilters();
        $this->getTableFiltersForm()->fill();
    }

    protected function getCachedTable(): Table
    {
        return $this->table;
    }

    protected function getTable(): Table
    {
        return $this->makeTable()
            ->emptyStateDescription($this->getTableEmptyStateDescription())
            ->emptyStateHeading($this->getTableEmptyStateHeading())
            ->emptyStateIcon($this->getTableEmptyStateIcon())
            ->emptyStateView($this->getTableEmptyStateView())
            ->enablePagination($this->isTablePaginationEnabled())
            ->recordsPerPageSelectOptions($this->getTableRecordsPerPageSelectOptions());
    }

    protected function getForms(): array
    {
        return [
            'mountedTableActionForm' => $this->makeForm()->statePath('mountedTableActionData'),
            'mountedTableBulkActionForm' => $this->makeForm()->statePath('mountedTableBulkActionData'),
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
