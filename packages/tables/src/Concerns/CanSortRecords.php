<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanSortRecords
{
    public $tableSort = [];

    public function sortTable(?string $column = null): void
    {
        $existingColumn = $this->tableSort[0] ?? null;
        $existingDirection = $this->tableSort[1] ?? null;

        if ($column === $existingColumn) {
            if ($existingDirection === 'asc') {
                $direction = 'desc';
            } elseif ($existingDirection === 'desc') {
                $column = null;
                $direction = null;
            } else {
                $direction = 'asc';
            }
        } else {
            $direction = 'asc';
        }

        $this->tableSort = [$column, $direction];
    }

    public function updatedTableSort(): void
    {
        $this->selected = [];

        $this->resetPage();
    }

    protected function applySortingToTableQuery(Builder $query): Builder
    {
        $columnName = $this->getTableSortColumn();

        if (! $columnName) {
            return $query;
        }

        $column = $this->getCachedTableColumn($columnName);

        if (! $column) {
            return $query;
        }

        $column->applySort($query, $this->getTableSortDirection());

        return $query;
    }

    protected function getTableSortColumn(): ?string
    {
        return $this->tableSort[0] ?? null;
    }

    protected function getTableSortDirection(): string
    {
        return $this->tableSort[1] ?? 'asc';
    }
}
