<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanSortRecords
{
    /**
     * @var array<string, string> | string | null
     */
    public array | string | null $tableSort = null;

    public function sortTable(?string $column = null, ?string $direction = null, bool $isMultiSort = false): void
    {
        if (blank($column)) {
            $this->tableSort = null;

            $this->updatedTableSort();

            return;
        }

        $sorts = $this->getTableSorts();

        $currentDirection = $isMultiSort
            ? ($sorts[$column] ?? null)
            : ((count($sorts) === 1) && (array_key_first($sorts) === $column) ? $sorts[$column] : null);

        if ($currentDirection) {
            $direction ??= match ($currentDirection) {
                'asc' => 'desc',
                'desc' => null,
                default => 'asc',
            };
        } else {
            $direction ??= 'asc';
        }

        if ($isMultiSort) {
            if ($direction) {
                $sorts[$column] = $this->normalizeTableSortDirection($direction) ?? 'asc';
            } else {
                unset($sorts[$column]);
            }
        } elseif ($direction) {
            $sorts = [$column => $this->normalizeTableSortDirection($direction) ?? 'asc'];
        } else {
            $sorts = [];
        }

        $this->tableSort = $this->normalizeTableSortForStorage($sorts);

        $this->updatedTableSort();
    }

    /**
     * @return array<string, string>
     */
    public function getTableSorts(): array
    {
        if (blank($this->tableSort)) {
            return [];
        }

        if (is_array($this->tableSort)) {
            $sorts = [];

            foreach ($this->tableSort as $column => $direction) {
                if (blank($column)) {
                    continue;
                }

                $sorts[$column] = $this->normalizeTableSortDirection($direction) ?? 'asc';
            }

            return $sorts;
        }

        $column = (string) str($this->tableSort)->before(':');

        if (blank($column)) {
            return [];
        }

        return [$column => $this->getTableSortDirectionFromString($this->tableSort) ?? 'asc'];
    }

    public function getTableSortColumn(): ?string
    {
        if (blank($sorts = $this->getTableSorts())) {
            return null;
        }

        return array_key_first($sorts);
    }

    public function getTableSortDirection(): ?string
    {
        if (blank($sorts = $this->getTableSorts())) {
            return null;
        }

        return reset($sorts);
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

        $tableSorts = $this->getTableSorts();
        $tableSortColumns = array_keys($tableSorts);

        foreach ($tableSorts as $tableSortColumn => $sortDirection) {
            if (! $column = $this->getTable()->getSortableVisibleColumn($tableSortColumn)) {
                continue;
            }

            $column->applySort($query, $sortDirection);
        }

        $sortDirection = ($this->getTable()->getDefaultSortDirection() ?? $this->getTableSortDirection()) === 'desc' ? 'desc' : 'asc';
        $defaultSort = $this->getTable()->getDefaultSort($query, $sortDirection);

        if (
            is_string($defaultSort) &&
            (! in_array($defaultSort, $tableSortColumns, strict: true)) &&
            ($sortColumn = $this->getTable()->getSortableVisibleColumn($defaultSort))
        ) {
            $sortColumn->applySort($query, $sortDirection);
        } elseif (
            is_string($defaultSort) &&
            (! in_array($defaultSort, $tableSortColumns, strict: true))
        ) {
            $query->orderBy($defaultSort, $sortDirection);
        }

        if ($defaultSort instanceof Builder) {
            $query = $defaultSort;
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

    public function getTableSortSessionKey(): string
    {
        $table = md5($this::class);

        return "tables.{$table}_sort";
    }

    protected function getTableSortDirectionFromString(string $sort): ?string
    {
        if (! str($sort)->contains(':')) {
            return 'asc';
        }

        return $this->normalizeTableSortDirection((string) str($sort)->after(':'));
    }

    protected function normalizeTableSortDirection(mixed $direction): ?string
    {
        if (! is_scalar($direction)) {
            return null;
        }

        return match (strtolower(strval($direction))) {
            'asc' => 'asc',
            'desc' => 'desc',
            default => null,
        };
    }

    /**
     * @param  array<string, string>  $sorts
     * @return array<string, string> | string | null
     */
    protected function normalizeTableSortForStorage(array $sorts): array | string | null
    {
        if (blank($sorts)) {
            return null;
        }

        if (count($sorts) === 1) {
            $column = array_key_first($sorts);

            return "{$column}:{$sorts[$column]}";
        }

        return $sorts;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function shouldPersistTableSortInSession(): bool
    {
        return false;
    }
}
