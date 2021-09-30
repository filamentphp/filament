<?php

namespace Filament\Tables\Concerns;

use Illuminate\Support\Str;

trait CanSearchRecords
{
    public $isSearchable = true;

    public $search = '';

    protected $hasSearchQueriesApplied = false;

    public function updatedSearch()
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    protected function applySearch($query)
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

    public function isSearchable()
    {
        return $this->isSearchable && collect($this->getTable()->getColumns())
                ->filter(fn ($column) => $column->isSearchable())
                ->count();
    }

    protected function isRelationshipSearch($column)
    {
        return Str::of($column)->contains('.');
    }

    protected function applyRelationshipSearch($query, $searchColumn)
    {
        $relationshipName = (string) Str::of($searchColumn)->beforeLast('.');
        $relatedColumnName = (string) Str::of($searchColumn)->afterLast('.');

        return $query->{$this->hasNoSearchQueriesApplied() ? 'whereHas' : 'orWhereHas'}(
            $relationshipName,
            fn ($query) => $query->where($relatedColumnName, $this->getSearchOperator(), "%{$this->getSearch()}%"),
        );
    }

    protected function hasNoSearchQueriesApplied()
    {
        return ! $this->hasSearchQueriesApplied;
    }

    protected function getSearchOperator()
    {
        return [
                'pgsql' => 'ilike',
            ][$this->getQuery()->getConnection()->getDriverName()] ?? 'like';
    }

    public function getSearch()
    {
        return Str::lower($this->search);
    }

    protected function hasSearchQueriesApplied()
    {
        return $this->hasSearchQueriesApplied;
    }
}
