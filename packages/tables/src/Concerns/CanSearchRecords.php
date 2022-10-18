<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

trait CanSearchRecords
{
    public $tableColumnSearchQueries = [];

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

    public function updatedTableColumnSearchQueries($value, $key): void
    {
        if (blank($value)) {
            unset($this->tableColumnSearchQueries[$key]);
        }

        if ($this->getTable()->persistsColumnSearchInSession()) {
            session()->put(
                $this->getTableColumnSearchSessionKey(),
                $this->tableColumnSearchQueries,
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
        foreach ($this->getTableColumnSearchQueries() as $column => $search) {
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

    protected function getTableSearch(): string
    {
        return trim(strtolower($this->tableSearch));
    }

    protected function getTableColumnSearchQueries(): array
    {
        // Example input of `$this->tableColumnSearchQueries`:
        // [
        //     'number' => '12345 ',
        //     'customer' => [
        //         'name' => ' john Smith',
        //     ],
        // ]

        // The `$this->tableColumnSearchQueries` array is potentially nested.
        // So, we iterate through it deeply:
        $iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($this->tableColumnSearchQueries),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $searchQueries = [];
        $path = [];

        foreach ($iterator as $key => $value) {
            $path[$iterator->getDepth()] = $key;

            if (is_array($value)) {
                continue;
            }

            // Nested array keys are flattened into `dot.syntax`.
            $searchQueries[
                implode('.', array_slice($path, 0, $iterator->getDepth() + 1))
            ] = trim(strtolower($value));
        }

        return $searchQueries;

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
