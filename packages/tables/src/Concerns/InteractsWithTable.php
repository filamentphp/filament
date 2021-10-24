<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Table;
use Livewire\WithPagination;

trait InteractsWithTable
{
    use CanPaginateRecords;
    use CanSearchRecords;
    use HasColumns;
    use HasEmptyState;
    use HasFilters;
    use HasRecords;
    use WithPagination;

    protected Table $table;

    public function bootInteractsWithTable(): void
    {
        $this->table = $this->makeTable();

        $this->cacheTableColumns();

        $this->cacheTableFilters();
        $this->filtersForm->fill();
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    protected function makeTable(): Table
    {
        return Table::make($this);
    }
}
