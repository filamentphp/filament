<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanPaginateRecords
{
    public $tableRecordsPerPage = 10;

    protected bool $isTablePaginationEnabled = true;

    protected array $tableRecordsPerPageSelectOptions = [5, 10, 25, 50];

    public function getTableRecordsPerPage(): int
    {
        return intval($this->tableRecordsPerPage);
    }

    public function isTablePaginationEnabled(): bool
    {
        return $this->isTablePaginationEnabled;
    }

    public function updatedTableRecordsPerPage()
    {
        $this->selected = [];

        $this->resetPage();
    }
}
