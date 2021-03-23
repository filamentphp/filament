<?php

namespace Filament\Tables\Concerns;

use Illuminate\Support\Str;

trait CanFilterRecords
{
    public $filter = null;

    public $isFilterable = true;

    public $isSearchable = true;

    public $search = '';

    public function isFilterable()
    {
        return $this->isFilterable && count($this->getTable()->getFilters());
    }

    public function isSearchable()
    {
        return $this->isSearchable && collect($this->getTable()->getColumns())
                ->filter(fn ($column) => $column->isSearchable())
                ->count();
    }

    public function updatedFilter()
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    protected function applyFilters($query)
    {
        if (
            ! $this->isFilterable() ||
            $this->filter === '' ||
            $this->filter === null
        ) {
            return $query;
        }

        collect($this->getTable()->getFilters())
            ->filter(fn ($filter) => $filter->getName() === $this->filter)
            ->each(function ($filter) use (&$query) {
                $query = $filter->apply($query);
            });

        $query = $this->applySearch($query);

        return $query;
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
            ->each(function ($column, $index) use (&$query) {
                $search = Str::lower($this->search);
                $searchOperator = [
                        'pgsql' => 'ilike',
                    ][$query->getConnection()->getDriverName()] ?? 'like';

                $first = $index === 0;

                if (Str::of($column->getName())->contains('.')) {
                    $relationship = (string) Str::of($column->getName())->beforeLast('.');

                    $query = $query->{$first ? 'whereHas' : 'orWhereHas'}(
                        $relationship,
                        function ($query) use ($column, $search, $searchOperator) {
                            $columnName = (string) Str::of($column->getName())->afterLast('.');

                            return $query->where($columnName, $searchOperator, "%{$search}%");
                        },
                    );

                    return;
                }

                $query = $query->{$first ? 'where' : 'orWhere'}(
                    fn ($query) => $query->where($column->getName(), $searchOperator, "%{$search}%"),
                );
            });

        return $query;
    }
}
