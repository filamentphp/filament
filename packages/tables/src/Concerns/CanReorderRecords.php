<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

trait CanReorderRecords
{
    public bool $isTableReordering = false;

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
                    $this->getTableRecord($recordKey)->getRelationValue($relationship->getPivotAccessor())->update([
                        $orderColumn => $index + 1,
                    ]);
                }

                return;
            }

            $model = app($this->getTable()->getModel());
            $modelKeyName = $model->getKeyName();
            $wrappedModelKeyName = $model->getConnection()?->getQueryGrammar()?->wrap($modelKeyName) ?? $modelKeyName;

            $model
                ->newModelQuery()
                ->whereIn($modelKeyName, array_values($order))
                ->update([
                    $orderColumn => DB::raw(
                        'case ' . collect($order)
                            ->map(fn ($recordKey, int $recordIndex): string => 'when ' . $wrappedModelKeyName . ' = ' . DB::getPdo()->quote($recordKey) . ' then ' . ($recordIndex + 1))
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
