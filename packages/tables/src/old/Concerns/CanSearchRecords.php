<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait CanSearchRecords
{
    public bool $isSearchable = true;

    public string $search = '';

    protected bool $hasSearchQueriesApplied = false;

    public function updatedSearch(): void
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    protected function applySearch(Builder $query): Builder
    {
        if (
            ! $this->isSearchable() ||
            $this->search === '' ||
            $this->search === null
        ) {
            return $query;
        }

        $query->where(function ($query) {
            collect($this->getTable()->getColumns())
                ->filter(fn ($column) => $column->isSearchable())
                ->each(function ($column) use (&$query) {
                    foreach ($column->getSearchColumns() as $searchColumn) {
                        if ($this->isRelationshipSearch($searchColumn)) {
                            $query = $this->applyRelationshipSearch($query, $searchColumn);
                        } else {
                            $query->{$this->hasNoSearchQueriesApplied() ? 'where' : 'orWhere'}(
                                $searchColumn,
                                $this->getSearchOperator(),
                                "%{$this->getSearch()}%"
                            );
                        }

                        $this->hasSearchQueriesApplied = true;
                    }
                });
        });

        return $query;
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable && collect($this->getTable()->getColumns())
                ->filter(fn ($column) => $column->isSearchable())
                ->count();
    }

    protected function isRelationshipSearch(string $column): bool
    {
        return Str::of($column)->contains('.');
    }

    protected function applyRelationshipSearch(Builder $query, string $searchColumn): Builder
    {
        $relationshipName = (string) Str::of($searchColumn)->beforeLast('.');
        $relatedColumnName = (string) Str::of($searchColumn)->afterLast('.');

        return $query->{$this->hasNoSearchQueriesApplied() ? 'whereHas' : 'orWhereHas'}(
            $relationshipName,
            fn ($query) => $query->where($relatedColumnName, $this->getSearchOperator(), "%{$this->getSearch()}%"),
        );
    }

    protected function hasNoSearchQueriesApplied(): bool
    {
        return ! $this->hasSearchQueriesApplied;
    }

    protected function getSearchOperator(): string
    {
        return [
            'pgsql' => 'ilike',
        ][$this->getQuery()->getConnection()->getDriverName()] ?? 'like';
    }

    public function getSearch(): string
    {
        return Str::lower($this->search);
    }

    protected function hasSearchQueriesApplied(): bool
    {
        return $this->hasSearchQueriesApplied;
    }
}
