<?php

namespace Filament\Tables\Concerns;

trait CanPaginateRecords
{
    public $tableRecordsPerPage = 10;

    public function updatedTableRecordsPerPage(): void
    {
        $this->resetPage();
    }

    protected function getTableRecordsPerPage(): int
    {
        return intval($this->tableRecordsPerPage);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }
}
