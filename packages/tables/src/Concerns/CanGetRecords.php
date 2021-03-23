<?php

namespace Filament\Tables\Concerns;

use Illuminate\Support\Str;

trait CanGetRecords
{
    public function getRecords()
    {
        $query = static::getQuery();

        if ($this->isFilterable() && $this->filter !== '' && $this->filter !== null) {
            collect($this->getTable()->getFilters())
                ->filter(fn ($filter) => $filter->getName() === $this->filter)
                ->each(function ($filter) use (&$query) {
                    $query = $filter->apply($query);
                });
        }

        if ($this->isSearchable() && $this->search !== '' && $this->search !== null) {
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
        }

        if ($this->isSortable() && $this->sortColumn !== '' && $this->sortColumn !== null) {
            if (Str::of($this->sortColumn)->contains('.')) {
                $relationship = (string) Str::of($this->sortColumn)->beforeLast('.');

                $query = $query->with([
                    $relationship => function ($query) {
                        return $query->orderBy(
                            (string) Str::of($this->sortColumn)->afterLast('.'),
                            $this->sortDirection,
                        );
                    },
                ]);
            } else {
                $query = $query->orderBy($this->sortColumn, $this->sortDirection);
            }
        }

        if (! $this->hasPagination()) {
            return $query->get();
        }

        return $query->paginate($this->recordsPerPage);
    }
}
