<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

trait CanSearchRecords
{
    public $tableColumnSearches = [];

    public $tableSearch = '';

    public function updatedTableSearch(): void
    {
        if ($this->getTable()->persistsSearchInSession()) {
            session()->put(
                $this->getTableSearchSessionKey(),
                $this->tableSearch,
            );
        }

        $this->deselectAllTableRecords();

        $this->resetPage();
    }

    public function updatedTableColumnSearches($value, $key): void
    {
        if (blank($value)) {
            unset($this->tableColumnSearches[$key]);
        }

        if ($this->getTable()->persistsColumnSearchInSession()) {
            session()->put(
                $this->getTableColumnSearchSessionKey(),
                $this->tableColumnSearches,
            );
        }

        $this->deselectAllTableRecords();

        $this->resetPage();
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $this->applyColumnSearchToTableQuery($query);
        $this->applyGlobalSearchToTableQuery($query);

        return $query;
    }

    protected function applyColumnSearchToTableQuery(Builder $query): Builder
    {
        foreach ($this->getTableColumnSearches() as $column => $search) {
            if ($search === '') {
                continue;
            }

            $column = $this->getTable()->getColumn($column);

            if (! $column) {
                continue;
            }

            foreach (explode(' ', $search) as $searchWord) {
                $query->where(function (Builder $query) use ($column, $searchWord) {
                    $isFirst = true;

                    $column->applySearchConstraint(
                        $query,
                        $searchWord,
                        $isFirst,
                        isIndividual: true,
                    );
                });
            }
        }

        return $query;
    }

    protected function applyGlobalSearchToTableQuery(Builder $query): Builder
    {
        $search = $this->getTableSearch();

        if ($search === '') {
            return $query;
        }

        foreach (explode(' ', $search) as $searchWord) {
            $query->where(function (Builder $query) use ($searchWord) {
                $isFirst = true;

                foreach ($this->getTable()->getColumns() as $column) {
                    $column->applySearchConstraint(
                        $query,
                        $searchWord,
                        $isFirst,
                    );
                }
            });
        }

        return $query;
    }

    public function getTableSearch(): string
    {
        return trim(strtolower($this->tableSearch));
    }

    protected function castTableColumnSearches(array $searches): array
    {
        return array_map(
            fn ($search): array | string => is_array($search) ?
                $this->castTableColumnSearches($search) :
                strval($search),
            $searches,
        );
    }

    public function getTableColumnSearches(): array
    {
        // Example input of `$this->tableColumnSearches`:
        // [
        //     'number' => '12345 ',
        //     'customer' => [
        //         'name' => ' john Smith',
        //     ],
        // ]

        // The `$this->tableColumnSearches` array is potentially nested.
        // So, we iterate through it deeply:
        $iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($this->tableColumnSearches),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $searches = [];
        $path = [];

        foreach ($iterator as $key => $value) {
            $path[$iterator->getDepth()] = $key;

            if (is_array($value)) {
                continue;
            }

            // Nested array keys are flattened into `dot.syntax`.
            $searches[
                implode('.', array_slice($path, 0, $iterator->getDepth() + 1))
            ] = trim(strtolower($value));
        }

        return $searches;

        // Example output:
        // [
        //     'number' => '12345',
        //     'customer.name' => 'john smith',
        // ]
    }

    public function getTableSearchSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_search";
    }

    public function getTableColumnSearchSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_column_search";
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function shouldPersistTableSearchInSession(): bool
    {
        return false;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function shouldPersistTableColumnSearchInSession(): bool
    {
        return false;
    }
}
