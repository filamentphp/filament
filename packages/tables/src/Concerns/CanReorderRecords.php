<?php

namespace Filament\Tables\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait CanReorderRecords
{
    public bool $isTableReordering = false;

    /**
     * @param  array<int | string>  $order
     */
    public function reorderTable(array $order, int | string | null $draggedRecordKey = null): void
    {
        if (! $this->getTable()->isReorderable()) {
            return;
        }

        $orderColumn = (string) str($this->getTable()->getReorderColumn())->afterLast('.');

        DB::transaction(function () use ($order, $orderColumn): void {
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
                    $orderColumn => new Expression(
                        'case ' . collect($order)
                            ->when($this->getTable()->getReorderDirection() === 'desc', fn (Collection $order) => $order->reverse()->values())
                            ->map(fn ($recordKey, int $recordIndex): string => 'when ' . $wrappedModelKeyName . ' = ' . DB::getPdo()->quote($recordKey) . ' then ' . ($recordIndex + 1))
                            ->implode(' ') . ' end'
                    ),
                ]);
        });

        $this->flushCachedTableRecords();
    }

    /**
     * @param  array<int, array{id: int | string, position: int, parent?: int | string | null}>  $order
     */
    public function reorderTreeTable(array $order): void
    {
        $table = $this->getTable();

        if (! $table->hasTree() || ! $table->isReorderable()) {
            return;
        }

        $tree = $table->getTree();

        if (($callback = $tree?->getReorderUsing()) instanceof Closure) {
            $table->evaluate($callback, [
                'order' => $order,
                'table' => $table,
                'livewire' => $this,
            ]);

            $this->flushCachedTableRecords();

            return;
        }

        $orderColumn = (string) str($table->getReorderColumn())->afterLast('.');
        $parentColumn = $tree?->getParentColumn();

        DB::transaction(function () use ($order, $orderColumn, $parentColumn, $table): void {
            $relationship = $table->getRelationship();

            if (
                ($relationship instanceof BelongsToMany) &&
                in_array($orderColumn, $relationship->getPivotColumns())
            ) {
                foreach ($order as $payload) {
                    if (! isset($payload['id'], $payload['position'])) {
                        continue;
                    }

                    $record = $this->getTableRecord((string) $payload['id']);

                    if (! $record) {
                        continue;
                    }

                    $record->getRelationValue($relationship->getPivotAccessor())->update([
                        $orderColumn => (int) $payload['position'],
                    ]);
                }

                return;
            }

            $model = app($table->getModel());
            $modelKeyName = $model->getKeyName();

            foreach ($order as $payload) {
                if (! isset($payload['id'], $payload['position'])) {
                    continue;
                }

                $update = [
                    $orderColumn => (int) $payload['position'],
                ];

                if ($parentColumn) {
                    $update[$parentColumn] = $payload['parent'] ?? null;
                }

                $model
                    ->newModelQuery()
                    ->where($modelKeyName, $payload['id'])
                    ->update($update);
            }
        });

        $this->flushCachedTableRecords();
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
