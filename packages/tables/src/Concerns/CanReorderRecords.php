<?php

namespace Filament\Tables\Concerns;

trait CanReorderRecords
{
    public bool $isTableReordering = false;

    public function reorderTable(array $order): void
    {
        if (! $this->isTableReorderable()) {
            return;
        }

        $orderColumn = $this->getTableReorderColumn();

        foreach ($order as $index => $recordKey) {
            $this->resolveTableRecord($recordKey)->update([
                $orderColumn => $index + 1,
            ]);
        }
    }

    public function toggleTableReordering(): void
    {
        $this->isTableReordering = ! $this->isTableReordering;
    }

    public function isTableReordering(): bool
    {
        return $this->isTableReorderable() && $this->isTableReordering;
    }

    protected function isTablePaginationEnabledDuringReordering(): bool
    {
        return false;
    }

    protected function isTableReorderable(): bool
    {
        return filled($this->getTableReorderColumn());
    }

    protected function getTableReorderColumn(): ?string
    {
        return null;
    }
}
