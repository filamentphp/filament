<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanSortRecords
{
    public ?string $tableSort = null;

    public function sortTable(?string $column = null, ?string $direction = null): void
    {
        if ($column === $this->getTableSortColumn()) {
            $direction ??= match ($this->getTableSortDirection()) {
                'asc' => 'desc',
                'desc' => null,
                default => 'asc',
            };
        } else {
            $direction ??= 'asc';
        }

        $this->tableSort = $direction ? "{$column}:{$direction}" : null;

        $this->updatedTableSort();
    }

    public function getTableSortColumn(): ?string
    {
        if (blank($this->tableSort)) {
            return null;
        }

        return (string) str($this->tableSort)->before(':');
    }

    public function getTableSortDirection(): ?string
    {
        if (blank($this->tableSort)) {
            return null;
        }

        if (! str($this->tableSort)->contains(':')) {
            return 'asc';
        }

        return match ((string) str($this->tableSort)->after(':')) {
            'asc' => 'asc',
            'desc' => 'desc',
            default => null,
        };
    }

    public function updatedTableSort(): void
    {
        if ($this->getTable()->persistsSortInSession()) {
            session()->put(
                $this->getTableSortSessionKey(),
                $this->tableSort,
            );
        }

        $this->resetPage();
    }

    public function updatedTableSortDirection(): void
    {
        if ($this->getTable()->persistsSortInSession()) {
            session()->put(
                $this->getTableSortSessionKey(),
                $this->tableSort,
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
            return $query->orderBy($this->getTable()->getReorderColumn(), $this->getTable()->getReorderDirection());
        }

        if (
            $this->getTableSortColumn() &&
            $column = $this->getTable()->getSortableVisibleColumn($this->getTableSortColumn())
        ) {
            $sortDirection = $this->getTableSortDirection() === 'desc' ? 'desc' : 'asc';

            $column->applySort($query, $sortDirection);
        }

        $sortDirection = ($this->getTable()->getDefaultSortDirection() ?? $this->getTableSortDirection()) === 'desc' ? 'desc' : 'asc';
        $defaultSort = $this->getTable()->getDefaultSort($query, $sortDirection);

        if (
            is_string($defaultSort) &&
            ($defaultSort !== $this->getTableSortColumn()) &&
            ($sortColumn = $this->getTable()->getSortableVisibleColumn($defaultSort))
        ) {
            $sortColumn->applySort($query, $sortDirection);
        } elseif (is_string($defaultSort)) {
            $query->orderBy($defaultSort, $sortDirection);
        }

        if ($defaultSort instanceof Builder) {
            $query = $defaultSort;
        }

        if (filled($query->getQuery()->orders) && (! $this->getTable()->hasDefaultKeySort())) {
            return $query;
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
