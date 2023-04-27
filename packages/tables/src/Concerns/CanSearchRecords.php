<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

trait CanSearchRecords
{
    /**
     * @var array<string, string | array<string, string | null> | null>
     */
    public array $tableColumnSearches = [];

    /**
     * @var string | null
     */
    public $tableSearch = '';

    public function updatedTableSearch(): void
    {
        if ($this->getTable()->persistsSearchInSession()) {
            session()->put(
                $this->getTableSearchSessionKey(),
                $this->tableSearch,
            );
        }

        if ($this->getTable()->shouldDeselectAllRecordsWhenFiltered()) {
            $this->deselectAllTableRecords();
        }

        $this->resetPage();
    }

    /**
     * @param  string | null  $value
     */
    public function updatedTableColumnSearches($value = null, ?string $key = null): void
    {
        if (blank($value)) {
            unset($this->tableColumnSearches[$key]);
        }

        if ($this->getTable()->persistsColumnSearchesInSession()) {
            session()->put(
                $this->getTableColumnSearchesSessionKey(),
                $this->tableColumnSearches,
            );
        }

        if ($this->getTable()->shouldDeselectAllRecordsWhenFiltered()) {
            $this->deselectAllTableRecords();
        }

        $this->resetPage();
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $this->applyColumnSearchesToTableQuery($query);
        $this->applyGlobalSearchToTableQuery($query);

        return $query;
    }

    protected function applyColumnSearchesToTableQuery(Builder $query): Builder
    {
        foreach ($this->getTableColumnSearches() as $column => $search) {
            if ($search === '') {
                continue;
            }

            $column = $this->getTable()->getColumn($column);

            if (! $column) {
                continue;
            }

            if ($column->isHidden()) {
                continue;
            }

            if (! $column->isIndividuallySearchable()) {
                continue;
            }

            foreach (explode(' ', $search) as $searchWord) {
                $query->where(function (Builder $query) use ($column, $searchWord) {
                    $isFirst = true;

                    $column->applySearchConstraint(
                        $query,
                        $searchWord,
                        $isFirst,
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
                    if ($column->isHidden()) {
                        continue;
                    }

                    if (! $column->isGloballySearchable()) {
                        continue;
                    }

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

    /**
     * @param  array<string, string | array<string, string | null> | null>  $searches
     * @return array<string, string | array<string, string | null> | null>
     */
    protected function castTableColumnSearches(array $searches): array
    {
        return array_map(
            fn ($search): array | string => is_array($search) ?
                $this->castTableColumnSearches($search) :
                strval($search),
            $searches,
        );
    }

    /**
     * @return array<string, string | null>
     */
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

    public function hasTableColumnSearches(): bool
    {
        return collect($this->getTableColumnSearches())
            ->contains(fn (string $search): bool => filled($search));
    }

    public function getTableSearchSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_search";
    }

    public function getTableColumnSearchesSessionKey(): string
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
