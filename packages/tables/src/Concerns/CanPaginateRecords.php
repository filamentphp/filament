<?php

namespace Filament\Tables\Concerns;

trait CanPaginateRecords
{
    public $tableRecordsPerPage = 10;

    public function getTableRecordsPerPage(): int
    {
        return intval($this->tableRecordsPerPage);
    }

    public function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50];
    }

    public function isTablePaginationEnabled(): bool
    {
        return true;
    }

    public function updatedTableRecordsPerPage(): void
    {
        $this->resetPage();
    }
}
