<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
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

        $this->getTable()->callBeforeReordering($order);

        $orderColumn = (string) str($this->getTable()->getReorderColumn())->afterLast('.');

        DB::transaction(function () use ($order, $orderColumn): void {
            if (
                (($relationship = $this->getTable()->getRelationship()) instanceof BelongsToMany) &&
                in_array($orderColumn, $relationship->getPivotColumns())
            ) {
                $keyColumn = $this->getTable()->allowsDuplicates()
                    ? app($relationship->getPivotClass())->getKeyName()
                    : $relationship->getRelatedPivotKeyName();

                $connection = $relationship->getRelated()->getConnection();

                $relationship->newPivotQuery()
                    ->whereIn($keyColumn, array_values($order))
                    ->update([
                        $orderColumn => $this->makeTableReorderColumnExpression($order, $connection->getQueryGrammar()->wrap($keyColumn), $connection),
                    ]);

                return;
            }

            $model = app($this->getTable()->getModel());
            $modelKeyName = $model->getKeyName();
            $connection = $model->getConnection();

            $this->getTable()
                ->getQuery()
                ->whereIn($modelKeyName, array_values($order))
                ->update([
                    $orderColumn => $this->makeTableReorderColumnExpression($order, $connection->getQueryGrammar()->wrap($modelKeyName), $connection),
                ]);
        });

        $this->flushCachedTableRecords();

        $this->getTable()->callAfterReordering($order);
    }

    public function moveTableRecord(string $recordKey, string $direction): void
    {
        if (! $this->isTableReordering() || ! in_array($direction, ['up', 'down'], true)) {
            return;
        }

        $order = collect($this->getTableRecords()->all())
            ->map(fn (Model | array $record): string => $this->getTableRecordKey($record))
            ->values()
            ->all();

        $recordIndex = array_search($recordKey, $order, true);

        if ($recordIndex === false) {
            return;
        }

        $newRecordIndex = $recordIndex + (($direction === 'up') ? -1 : 1);

        if (! array_key_exists($newRecordIndex, $order)) {
            return;
        }

        [$order[$recordIndex], $order[$newRecordIndex]] = [$order[$newRecordIndex], $order[$recordIndex]];

        $this->reorderTable($order, $recordKey);
    }

    /**
     * @param  array<int | string>  $order
     */
    protected function makeTableReorderColumnExpression(array $order, string $wrappedKeyColumn, Connection $connection): Expression
    {
        return new Expression(
            'case ' . collect($order)
                ->when(
                    $this->getTable()->getReorderDirection() === 'desc',
                    fn (Collection $order): Collection => $order->reverse()->values(),
                )
                ->map(fn ($recordKey, int $recordIndex): string => 'when ' . $wrappedKeyColumn . ' = ' . $connection->escape($recordKey) . ' then ' . ($recordIndex + 1))
                ->implode(' ') . ' end'
        );
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
