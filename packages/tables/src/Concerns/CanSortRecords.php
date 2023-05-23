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
        if ($this->shouldPersistTableSortInSession()) {
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
        if ($this->isTableReordering()) {
            return $query->orderBy($this->getTableReorderColumn());
        }

        $sortColumn = $this->tableSortColumn ?? $this->getDefaultTableSortColumn();

        if (! $sortColumn) {
            return $query;
        }

        $sortDirection = $this->tableSortDirection ?? $this->getDefaultTableSortDirection();
        $sortDirection = $sortDirection === 'desc' ? 'desc' : 'asc';

        $column = $this->getCachedTableColumn($sortColumn);

        if ($column && (! $column->isHidden()) && $column->isSortable()) {
            $column->applySort($query, $sortDirection);

            return $query;
        }

        $this->applyDefaultSortingToTableQuery($query, $sortColumn, $sortDirection);

        return $query;
    }

    protected function applyDefaultSortingToTableQuery(Builder $query, string $sortColumn, string $sortDirection): Builder
    {
        if ($sortColumn !== $this->getDefaultTableSortColumn()) {
            return $query;
        }

        return $query->orderBy($sortColumn, $sortDirection);
    }

    public function getTableSortSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_sort";
    }

    protected function shouldPersistTableSortInSession(): bool
    {
        return false;
    }
}
