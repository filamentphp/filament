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

        if (! $this->tableSortColumn) {
            return $this->applyDefaultSortingToTableQuery($query);
        }

        $column = $this->getTable()->getColumn($this->tableSortColumn);

        if (! $column) {
            return $this->applyDefaultSortingToTableQuery($query);
        }

        if ($column->isHidden()) {
            return $this->applyDefaultSortingToTableQuery($query);
        }

        if (! $column->isSortable()) {
            return $this->applyDefaultSortingToTableQuery($query);
        }

        $sortDirection = $this->tableSortDirection === 'desc' ? 'desc' : 'asc';

        $column->applySort($query, $sortDirection);

        return $query;
    }

    protected function applyDefaultSortingToTableQuery(Builder $query): Builder
    {
        $sortDirection = $this->tableSortDirection === 'desc' ? 'desc' : 'asc';

        if ($sortColumn = $this->getTable()->getDefaultSortColumn()) {
            return $query->orderBy($sortColumn, $sortDirection);
        }

        if ($sortQueryUsing = $this->getTable()->getDefaultSortQuery()) {
            app()->call($sortQueryUsing, [
                'direction' => $sortDirection,
                'query' => $query,
            ]);

            return $query;
        }

        return $query->orderBy($query->getModel()->getQualifiedKeyName());
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
