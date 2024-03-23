<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

trait CanReorderRecords
{
    public bool $isTableReordering = false;

    /**
     * Handles the reordering of table rows, including the ID of the dragged item.
     * 
     * This method is designed to be overridden in specific implementations where
     * the ID of the dragged item is required for additional logic during the reordering process.
     * 
     * @param array $orderAndDraggedId An associative array containing the new order of item IDs ('order')
     *                                 and the ID of the dragged item ('draggedId').
     */
    public function reorderTableWithDragged($orderAndDraggedId): void
    {
        $order = $orderAndDraggedId['order'];
        // The $draggedId is intended for use in overridden methods where specific logic
        // regarding the dragged item is needed.
        $draggedId = $orderAndDraggedId['draggedId'];
        
        // Proceed with the reordering logic using the provided $order.
        $this->reorderTable($order);
    }

    /**
     * @param  array<int | string>  $order
     */
    public function reorderTable(array $order): void
    {
        if (! $this->getTable()->isReorderable()) {
            return;
        }

        $orderColumn = (string) str($this->getTable()->getReorderColumn())->afterLast('.');

        DB::transaction(function () use ($order, $orderColumn) {
            if (
                (($relationship = $this->getTable()->getRelationship()) instanceof BelongsToMany) &&
                in_array($orderColumn, $relationship->getPivotColumns())
            ) {
                foreach ($order as $index => $recordKey) {
                    $this->getTableRecord($recordKey)->{$relationship->getPivotAccessor()}->update([
                        $orderColumn => $index + 1,
                    ]);
                }

                return;
            }

            $model = app($this->getTable()->getModel());
            $modelKeyName = $model->getKeyName();

            $model
                ->newModelQuery()
                ->whereIn($modelKeyName, array_values($order))
                ->update([
                    $orderColumn => DB::raw(
                        'case ' . collect($order)
                            ->map(fn ($recordKey, int $recordIndex): string => 'when ' . $modelKeyName . ' = ' . DB::getPdo()->quote($recordKey) . ' then ' . ($recordIndex + 1))
                            ->implode(' ') . ' end'
                    ),
                ]);
        });
    }

    public function toggleTableReordering(): void
    {
        $this->isTableReordering = ! $this->isTableReordering;
    }

    public function isTableReordering(): bool
    {
        return $this->getTable()->isReorderable() && $this->isTableReordering;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function isTablePaginationEnabledWhileReordering(): bool
    {
        return false;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableReorderColumn(): ?string
    {
        return null;
    }
}
