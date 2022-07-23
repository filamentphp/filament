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

    protected function getDefaultTableSortColumn(): ?string
    {
        return null;
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return null;
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

        $columnName = $this->tableSortColumn;

        if (! $columnName) {
            return $query;
        }

        $direction = $this->tableSortDirection === 'desc' ? 'desc' : 'asc';

        if ($column = $this->getCachedTableColumn($columnName)) {
            $column->applySort($query, $direction);

            return $query;
        }

        if ($columnName === $this->getDefaultTableSortColumn()) {
            return $query->orderBy($columnName, $direction);
        }

        return $query;
    }
}
