<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanSortRecords
{
    public $tableSortColumn = null;

    public $tableSortDirection = null;

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

        $this->updatedTableSort();
    }

    public function getTableSortColumn(): ?string
    {
        return $this->tableSortColumn;
    }

    public function getTableSortDirection(): ?string
    {
        return $this->tableSortDirection;
    }

    public function updatedTableSort(): void
    {
        $this->resetPage();
    }

    protected function applySortingToTableQuery(Builder $query): Builder
    {
        if ($this->isTableReordering()) {
            return $query->orderBy($this->getTableReorderColumn());
        }

        $sortColumn = $this->tableSortColumn;

        if (! $sortColumn) {
            return $query->when(
                count($query->getQuery()->orders ?? []) === 0,
                fn (Builder $query) => $query->orderBy($query->getModel()->getQualifiedKeyName()),
            );
        }

        $sortDirection = $this->tableSortDirection === 'desc' ? 'desc' : 'asc';

        $column = $this->getTable()->getColumn($sortColumn);

        if ($column && (! $column->isHidden()) && $column->isSortable()) {
            $column->applySort($query, $sortDirection);

            return $query;
        }

        if ($sortColumn === $this->getDefaultTableSortColumn()) {
            return $query->orderBy($sortColumn, $sortDirection);
        }

        return $query;
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
}
