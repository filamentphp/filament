<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait CanSelectRecords
{
    public array $selectedTableRecords = [];

    public function deselectAllTableRecords(): void
    {
        $this->emitSelf('deselectAllTableRecords');
    }

    public function getAllTableRecordKeys(): array
    {
        $query = $this->getFilteredTableQuery();

        return $query->pluck($query->getModel()->getKeyName())->toArray();
    }

    public function getAllTableRecordsCount(): int
    {
        return $this->getFilteredTableQuery()->count();
    }

    public function getSelectedTableRecords(): Collection
    {
        return $this->getTableQuery()->find($this->selectedTableRecords);
    }

    public function isTableSelectionEnabled(): bool
    {
        return (bool) count($this->getCachedTableBulkActions());
    }
}
