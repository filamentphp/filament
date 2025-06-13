<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanSortRecords
{
    public ?string $tableSortColumn = null;

    public ?string $tableSortDirection = null;

    public function sortTable(?string $column = null, ?string $direction = null): void
    {
        if ($column === $this->tableSortColumn) {
            $direction ??= match ($this->tableSortDirection) {
                'asc' => 'desc',
                'desc' => null,
                default => 'asc',
            };
        } else {
            $direction ??= 'asc';
        }

        $this->tableSortColumn = $direction ? $column : null;
        $this->tableSortDirection = $direction;

        $this->updatedTableSortColumn();
    }

    public function getTableSortColumn(): ?string
    {
        return $this->tableSortColumn;
    }

    public function getTableSortDirection(): ?string
    {
        return $this->tableSortDirection;
    }

    public function updatedTableSortColumn(): void
    {
        if ($this->getTable()->persistsSortInSession()) {
            session()->put(
                $this->getTableSortSessionKey(),
                [
                    'column' => $this->tableSortColumn,
                    'direction' => $this->tableSortDirection,
                ],
            );
        }

        $this->resetPage();
    }

    public function updatedTableSortDirection(): void
    {
        if ($this->getTable()->persistsSortInSession()) {
            session()->put(
                $this->getTableSortSessionKey(),
                [
                    'column' => $this->tableSortColumn,
                    'direction' => $this->tableSortDirection,
                ],
            );
        }

        $this->resetPage();
    }

    protected function applySortingToTableQuery(Builder $query): Builder
    {
        if ($this->getTable()->isGroupsOnly()) {
            return $query;
        }

        if ($this->isTableReordering()) {
            return $query->orderBy($this->getTable()->getReorderColumn());
        }

        if (
            $this->tableSortColumn &&
            $column = $this->getTable()->getSortableVisibleColumn($this->tableSortColumn)
        ) {
            $sortDirection = $this->tableSortDirection === 'desc' ? 'desc' : 'asc';

            $column->applySort($query, $sortDirection);
        }

        $sortDirection = ($this->getTable()->getDefaultSortDirection() ?? $this->tableSortDirection) === 'desc' ? 'desc' : 'asc';
        $defaultSort = $this->getTable()->getDefaultSort($query, $sortDirection);

        if (
            is_string($defaultSort) &&
            ($defaultSort !== $this->tableSortColumn) &&
            ($sortColumn = $this->getTable()->getSortableVisibleColumn($defaultSort))
        ) {
            $sortColumn->applySort($query, $sortDirection);
        } elseif (is_string($defaultSort)) {
            $query->orderBy($defaultSort, $sortDirection);
        }

        if ($defaultSort instanceof Builder) {
            return $defaultSort;
        }

        $qualifiedKeyName = $query->getModel()->getQualifiedKeyName();

        foreach ($query->getQuery()->orders ?? [] as $order) {
            if (($order['column'] ?? null) === $qualifiedKeyName) {
                return $query;
            }

            if (
                is_string($order['column'] ?? null) &&
                str($order['column'] ?? null)->contains('.') &&
                str($order['column'] ?? null)->afterLast('.')->is(
                    str($qualifiedKeyName)->afterLast('.')
                )
            ) {
                return $query;
            }
        }

        return $query->orderBy($qualifiedKeyName);
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getDefaultTableSortColumn(): ?string
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getDefaultTableSortDirection(): ?string
    {
        return null;
    }

    public function getTableSortSessionKey(): string
    {
        $table = md5($this::class);

        return "tables.{$table}_sort";
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function shouldPersistTableSortInSession(): bool
    {
        return false;
    }
}
