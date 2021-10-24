<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Table;
use Livewire\WithPagination;

trait InteractsWithTable
{
    use CanPaginateRecords;
    use CanSearchRecords;
    use HasColumns;
    use HasRecords;
    use WithPagination;

    protected array $cachedTableColumns;

    protected bool $isTablePaginationEnabled = true;

    protected Table $table;

    public function bootInteractsWithTable(): void
    {
        $this->table = $this->makeTable();
        $this->cacheTableColumns();
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
