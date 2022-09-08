<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

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

    protected function getTableColumnSearchQueries(): array
    {
        return array_map(
            fn (string $search): string => trim(strtolower($search)),
            $this->tableColumnSearchQueries,
        );
    }
}
