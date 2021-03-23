<?php

namespace Filament\Tables;

use Illuminate\Support\Str;
use Livewire\WithPagination;

trait HasTable
{
    use WithPagination;

    public $filter = null;

    public $isFilterable = true;

    public $hasPagination = true;

    public $recordsPerPage = 25;

    public $search = '';

    public $isSearchable = true;

    public $selected = [];

    public $isSortable = true;

    public $sortColumn = null;

    public $sortDirection = 'asc';

    protected $table;

    public function deleteSelected()
    {
        static::getModel()::destroy($this->selected);

        $this->selected = [];
    }

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

    public function getTable()
    {
        if ($this->table !== null) {
            return $this->table;
        }

        return $this->table = $this->table(
            Table::for($this),
        );
    }

    public function hasPagination()
    {
        return $this->hasPagination;
    }

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

    public function isSortable()
    {
        return $this->isSortable && collect($this->getTable()->getColumns())
                ->filter(fn ($column) => $column->isSortable())
                ->count();
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

    protected function table(Table $table)
    {
        return $table;
    }

    public function toggleSelectAll()
    {
        $records = $this->getRecords();

        if (! $records->count()) {
            return;
        }

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

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    public function updatedRecordsPerPage()
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
}
