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

    public $selected = [];

    public $sortColumn = null;

    public $sortDirection = 'asc';

    public function deleteSelected()
    {
        static::getModel()::destroy($this->selected);

        $this->selected = [];
    }

    public function setPage($page)
    {
        $this->page = $page;

        $this->selected = [];
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            switch ($this->sortDirection) {
                case 'asc':
                    $this->sortDirection = 'desc';

                    break;
                case 'desc':
                    $this->sortColumn = null;
                    $this->sortDirection = 'asc';

                    break;
            }

            return;
        }

        $this->sortColumn = $column;
        $this->sortDirection = 'asc';
    }

    public function toggleSelectAll()
    {
        $records = $this->getRecords();

        if (! $records->count()) return;

        $keyName = $records->first()->getKeyName();

        if ($records->count() !== count($this->selected)) {
            $this->selected = $records->pluck($keyName)->all();
        } else {
            $this->selected = [];
        }
    }

    public function toggleSelected($record)
    {
        if (! in_array($record, $this->selected)) {
            $this->selected[] = $record;
        } else {
            $key = array_search($record, $this->selected);

            unset($this->selected[$key]);
        }
    }

    public function updatedFilter()
    {
        $this->selected = [];

        if (! $this->getTable()->pagination) return;

        $this->resetPage();
    }

    public function updatedRecordsPerPage()
    {
        $this->selected = [];

        if (! $this->getTable()->pagination) return;

        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->selected = [];

        if (! $this->getTable()->pagination) return;

        $this->resetPage();
    }

    public function getRecords()
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
                    $search = Str::lower($this->search);

                    $first = $index === 0;

                    if (Str::of($column->name)->contains('.')) {
                        $relationship = (string) Str::of($column->name)->beforeLast('.');

                        $query = $query->{$first ? 'whereHas' : 'orWhereHas'}(
                            $relationship,
                            function ($query) use ($column, $search) {
                                $columnName = (string) Str::of($column->name)->afterLast('.');

                                return $query->whereRaw("LOWER({$columnName}) LIKE ?", ["%{$search}%"]);
                            },
                        );

                        return;
                    }

                    $query = $query->{$first ? 'where' : 'orWhere'}(
                        fn ($query) => $query->whereRaw("LOWER({$column->name}) LIKE ?", ["%{$search}%"]),
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
