<?php

namespace Filament\Tables\Concerns;

use Illuminate\Support\Str;

trait CanSearchRecords
{
    public $isSearchable = true;

    public $search = '';

    protected $hasSearchesApplied = false;

    public function getSearch()
    {
        return Str::lower($this->search);
    }

    public function isSearchable()
    {
        return $this->isSearchable && collect($this->getTable()->getColumns())
                ->filter(fn ($column) => $column->isSearchable())
                ->count();
    }

    public function updatedSearch()
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    protected function applyRelationshipSearch($query, $searchColumn)
    {
        $relationshipName = (string) Str::of($searchColumn)->beforeLast('.');
        $relatedColumnName = (string) Str::of($searchColumn)->afterLast('.');

        return $query->{$this->hasNoSearchesApplied() ? 'whereHas' : 'orWhereHas'}(
            $relationshipName,
            fn ($query) => $query->where($relatedColumnName, $this->getSearchOperator(), "%{$this->getSearch()}%"),
        );
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

        collect($this->getTable()->getColumns())
            ->filter(fn ($column) => $column->isSearchable())
            ->each(function ($column) use (&$query) {
                foreach ($column->getSearchColumns() as $searchColumn) {
                    if ($this->isRelationshipSearch($searchColumn)) {
                        $query = $this->applyRelationshipSearch($query, $searchColumn);
                    } else {
                        $query = $query->{$this->hasNoSearchesApplied() ? 'where' : 'orWhere'}(
                            fn ($query) => $query->where($searchColumn, $this->getSearchOperator(), "%{$this->getSearch()}%"),
                        );
                    }

                    $this->hasSearchesApplied = true;
                }
            });

        return $query;
    }

    protected function getSearchOperator()
    {
        return [
            'pgsql' => 'ilike',
        ][$this->getQuery()->getConnection()->getDriverName()] ?? 'like';
    }

    protected function hasNoSearchesApplied()
    {
        return ! $this->hasSearchesApplied;
    }

    protected function hasSearchesApplied()
    {
        return $this->hasSearchesApplied;
    }

    protected function isRelationshipSearch($column)
    {
        return Str::of($column)->contains('.');
    }
}
