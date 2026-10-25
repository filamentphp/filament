<?php

namespace Filament\Tables\Concerns;

use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Grammars\SqlServerGrammar;

trait CanSortRecords
{
    public ?string $tableSort = null;

    #[Exposed]
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

    #[Unexposed]
    public function getTableSortColumn(): ?string
    {
        if (blank($this->tableSort)) {
            return null;
        }

        return (string) str($this->tableSort)->before(':');
    }

    #[Unexposed]
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

    #[Unexposed]
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

    #[Unexposed]
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

        $tableSortColumn = $this->getTableSortColumn();

        if (
            $tableSortColumn &&
            $column = $this->getTable()->getSortableVisibleColumn($tableSortColumn)
        ) {
            if ($column->isToggledHidden()) {
                $tableSortColumn = null;
            } else {
                $sortDirection = $this->getTableSortDirection() === 'desc' ? 'desc' : 'asc';

                $column->applySort($query, $sortDirection);
            }
        }

        $sortDirection = ($this->getTable()->getDefaultSortDirection() ?? $this->getTableSortDirection()) === 'desc' ? 'desc' : 'asc';
        $defaultSort = $this->getTable()->getDefaultSort($query, $sortDirection);

        if (
            is_string($defaultSort) &&
            ($defaultSort !== $tableSortColumn) &&
            ($sortColumn = $this->getTable()->getSortableVisibleColumn($defaultSort))
        ) {
            if ($sortColumn->isToggledHidden()) {
                if ($this->shouldApplyTableColumnRelationshipAggregates) {
                    $sortColumn->applyRelationshipAggregates($query);
                }

                if ($defaultSort === $this->getTableSortColumn()) {
                    $sortDirection = $this->getTableSortDirection() === 'desc' ? 'desc' : 'asc';
                }
            }

            $sortColumn->applySort($query, $sortDirection);
        } elseif (
            is_string($defaultSort) &&
            $defaultSort !== $tableSortColumn
        ) {
            $query->orderBy($defaultSort, $sortDirection);
        }

        if ($defaultSort instanceof Builder) {
            $query = $defaultSort;
        }

        if ($query->getQuery()->getGrammar() instanceof SqlServerGrammar) {
            $query->getQuery()->orders = collect($query->getQuery()->orders)
                ->unique(static function (array $order, int $index) use ($query): string {
                    $column = $order['column'] ?? null;

                    return is_string($column) ? $query->qualifyColumn($column) : "expression:{$index}";
                })
                ->values()
                ->all();
        }

        if (! $this->getTable()->hasDefaultKeySort()) {
            return $query;
        }

        $qualifiedKeyName = $query->getModel()->getQualifiedKeyName();

        foreach ($query->getQuery()->orders ?? [] as $order) {
            if (($order['column'] ?? null) === $qualifiedKeyName) {
                return $query;
            }

            if (
                is_string($order['column'] ?? null) &&
                str($order['column'] ?? null)->afterLast('.')->is(
                    str($qualifiedKeyName)->afterLast('.')
                )
            ) {
                return $query;
            }
        }

        return $query->orderBy($qualifiedKeyName, $sortDirection);
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

    #[Unexposed]
    public function getTableSortSessionKey(): string
    {
        return $this->getTableSessionKey('sort');
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function shouldPersistTableSortInSession(): bool
    {
        return false;
    }
}
