<?php

namespace Filament\Tables;

use Illuminate\Support\Str;
use Livewire\WithPagination;

trait HasTable
{
    use WithPagination;

    public $filter = null;

    public $recordsPerPage = 25;

    public $search = '';

    public $sortColumn = null;

    public $sortDirection = 'asc';

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            switch ($this->sortDirection) {
                case 'asc':
                    $this->sortDirection = 'desc';

                    break;
                case 'desc':
                    $this->reset([
                        'sortColumn',
                        'sortDirection',
                    ]);

                    break;
            }

            return;
        }

        $this->sortColumn = $column;
        $this->reset('sortDirection');
    }

    public function updatedRecordsPerPage()
    {
        if (! $this->getTable()->pagination) return;

        $this->resetPage();
    }

    public function updatedSearch()
    {
        if (! $this->getTable()->pagination) return;

        $this->resetPage();
    }

    protected function getRecords()
    {
        $query = static::getQuery();

        if ($this->getTable()->filterable && $this->filter !== '' && $this->filter !== null) {
            collect($this->getTable()->filters)
                ->filter(fn ($filter) => $filter->name === $this->filter)
                ->each(function ($filter) use (&$query) {
                    $callback = $filter->callback;

                    $query = $callback($query);
                });
        }

        if ($this->getTable()->searchable && $this->search !== '' && $this->search !== null) {
            collect($this->getTable()->columns)
                ->filter(fn ($column) => $column->isSearchable())
                ->each(function ($column, $index) use (&$query) {
                    $first = $index === 0;

                    if (Str::of($column->name)->contains('.')) {
                        $relationship = (string) Str::of($column->name)->beforeLast('.');

                        $query = $query->{$first ? 'whereHas' : 'orWhereHas'}(
                            $relationship,
                            function ($query) use ($column) {
                                return $query->where(
                                    (string) Str::of($column->name)->afterLast('.'),
                                    'like',
                                    "%{$this->search}%",
                                );
                            },
                        );

                        return;
                    }

                    $query = $query->{$first ? 'where' : 'orWhere'}(
                        $column->name,
                        'like',
                        "%{$this->search}%"
                    );
                });
        }

        if ($this->getTable()->sortable && $this->sortColumn !== '' && $this->sortColumn !== null) {
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

        if (! $this->getTable()->pagination) {
            return $query->get();
        }

        return $query->paginate($this->recordsPerPage);
    }
}
