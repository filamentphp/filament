<?php

namespace Filament\Tables;

use Illuminate\Support\Traits\Tappable;

class Table
{
    use Tappable;

    protected $columns = [];

    protected $context;

    protected $filters = [];

    protected $hasPagination = true;

    protected $isFilterable = true;

    protected $isSearchable = true;

    protected $isSortable = true;

    protected $primaryColumnAction;

    protected $primaryColumnUrl;

    protected $recordActions = [];

    public static function make()
    {
        return new static();
    }

    public function columns($columns)
    {
        $this->columns = collect(value($columns))
            ->map(function ($column) {
                return $column->table($this);
            })
            ->toArray();

        return $this;
    }

    public function context($context)
    {
        $this->context = $context;

        return $this;
    }

    public function disableFiltering()
    {
        $this->isFilterable = false;

        return $this;
    }

    public function disablePagination()
    {
        $this->hasPagination = false;

        return $this;
    }

    public function disableSearching()
    {
        $this->isSearchable = false;

        return $this;
    }

    public function disableSorting()
    {
        $this->isSortable = false;

        return $this;
    }

    public function filterable($filterable)
    {
        $this->isFilterable = $filterable;

        return $this;
    }

    public function filters($filters)
    {
        $this->filters = collect(value($filters))
            ->map(function ($filter) {
                return $filter->table($this);
            })
            ->toArray();

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getContext()
    {
        return $this->columns;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getPrimaryColumnAction($record = null)
    {
        $action = $this->primaryColumnAction;

        if ($record && is_callable($action)) {
            return $action($record);
        }

        return $action;
    }

    public function getPrimaryColumnUrl($record = null)
    {
        $url = $this->primaryColumnUrl;

        if ($record && is_callable($url)) {
            return $url($record);
        }

        return $url;
    }

    public function getRecordActions()
    {
        return $this->recordActions;
    }

    public function getVisibleColumns()
    {
        $columns = collect($this->getColumns())
            ->filter(fn ($column) => ! $column->isHidden())
            ->toArray();

        return $columns;
    }

    public function getVisibleFilters()
    {
        $filters = collect($this->getFilters())
            ->filter(fn ($filter) => ! $filter->isHidden())
            ->toArray();

        return $filters;
    }

    public function hasPagination()
    {
        return $this->hasPagination;
    }

    public function isFilterable()
    {
        return $this->isFilterable && count($this->getFilters());
    }

    public function isSearchable()
    {
        return $this->isSearchable && collect($this->getColumns())
                ->filter(fn ($column) => $column->isSearchable())
                ->count();
    }

    public function isSortable()
    {
        return $this->isSortable && collect($this->columns)
                ->filter(fn ($column) => $column->isSortable())
                ->count();
    }

    public function pagination($enabled)
    {
        $this->pagination = $enabled;

        return $this;
    }

    public function primaryColumnAction($action)
    {
        $this->primaryColumnAction = $action;

        return $this;
    }

    public function primaryColumnUrl($url)
    {
        $this->primaryColumnUrl = $url;

        return $this;
    }

    public function recordActions($actions)
    {
        $this->recordActions = collect(value($actions))
            ->map(function ($action) {
                return $action->table($this);
            })
            ->toArray();

        return $this;
    }

    public function searchable($searchable)
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function sortable($sortable)
    {
        $this->sortable = $sortable;

        return $this;
    }
}
