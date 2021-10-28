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

    protected function getTableRecordsPerPageSelectOptions(): ?array
    {
        return null;
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }
}
