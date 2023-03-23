<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

trait CanSearchRecords
{
    public $tableColumnSearchQueries = [];

    public $tableSearchQuery = '';

    public function isTableSearchable(): bool
    {
        foreach ($this->getCachedTableColumns() as $column) {
            if (! $column->isGloballySearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function isTableSearchableByColumn(): bool
    {
        foreach ($this->getCachedTableColumns() as $column) {
            if (! $column->isIndividuallySearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function updatedTableSearchQuery(): void
    {
        if ($this->shouldPersistTableSearchInSession()) {
            session()->put(
                $this->getTableSearchSessionKey(),
                $this->tableSearchQuery,
            );
        }

        if ($this->shouldDeselectAllRecordsWhenTableFiltered()) {
            $this->deselectAllTableRecords();
        }

        $this->resetPage();
    }

    public function updatedTableColumnSearchQueries($value = null, $key = null): void
    {
        if (blank($value)) {
            unset($this->tableColumnSearchQueries[$key]);
        }

        if ($this->shouldPersistTableColumnSearchInSession()) {
            session()->put(
                $this->getTableColumnSearchSessionKey(),
                $this->tableColumnSearchQueries,
            );
        }

        if ($this->shouldDeselectAllRecordsWhenTableFiltered()) {
            $this->deselectAllTableRecords();
        }

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

            $column = $this->getCachedTableColumn($column);

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
        $search = $this->getTableSearchQuery();

        if ($search === '') {
            return $query;
        }

        foreach (explode(' ', $search) as $searchWord) {
            $query->where(function (Builder $query) use ($searchWord) {
                $isFirst = true;

                foreach ($this->getCachedTableColumns() as $column) {
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

    protected function getTableSearchQuery(): string
    {
        return trim(strtolower($this->tableSearchQuery));
    }

    protected function castTableColumnSearchQueries(array $searchQueries): array
    {
        return array_map(
            fn ($search): array | string => is_array($search) ?
                $this->castTableColumnSearchQueries($search) :
                strval($search),
            $searchQueries,
        );
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

    public function hasTableColumnSearches(): bool
    {
        return collect($this->getTableColumnSearchQueries())
            ->contains(fn (string $search): bool => filled($search));
    }

    public function getTableSearchSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_search";
    }

    protected function shouldPersistTableSearchInSession(): bool
    {
        return false;
    }

    public function getTableColumnSearchSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_column_search";
    }

    protected function shouldPersistTableColumnSearchInSession(): bool
    {
        return false;
    }
}
