<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Contracts\HasRelationshipTable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait CanReorderRecords
{
    public bool $isTableReordering = false;

    public function reorderTable(array $order): void
    {
        if (! $this->isTableReorderable()) {
            return;
        }

        $orderColumn = $this->getTableReorderColumn();
        $maxOrderRecord = collect($order)->max();

        if (
            $this instanceof HasRelationshipTable &&
            (($relationship = $this->getRelationship()) instanceof BelongsToMany) &&
            in_array($orderColumn, $relationship->getPivotColumns())
        ) {
            foreach ($order as $index => $recordKey) {
                $this->getTableRecord($recordKey)->{$relationship->getPivotAccessor()}->update(
                    $this->newOrderPosition($index, $orderColumn, $maxOrderRecord)
                );
            }

            return;
        }

        foreach ($order as $index => $recordKey) {
            $this->getTableRecord($recordKey)->update(
                $this->newOrderPosition($index, $orderColumn, $maxOrderRecord)
            );
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

    protected function isTablePaginationEnabledWhileReordering(): bool
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

    protected function preserveTableOrderWhileReordering(): bool
    {
        return true;
    }

    private function newOrderPosition($index, $orderColumn, $maxOrderRecord): array
    {
        return [
            $orderColumn => ($this->tableSortDirection === 'desc' &&
                $this->preserveTableOrderWhileReordering())
                ? $maxOrderRecord - $index
                : $index + 1,
        ];
    }
}
