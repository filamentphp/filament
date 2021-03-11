<?php

namespace Filament\Tables;

use Illuminate\Support\Arr;

class Table
{
    public $columns = [];

    public $filterable = true;

    public $filters = [];

    public $pagination = true;

    public $primaryColumnAction;

    public $primaryColumnUrl;

    public $recordActions = [];

    public $searchable = true;

    public $sortable = true;

    public static function make()
    {
        return new static();
    }

    public function columns($columns)
    {
        $this->columns = value($columns);

        return $this;
    }

    public function filters($filters)
    {
        $this->filters = value($filters);

        return $this;
    }

    public function context($context)
    {
        $this->columns = collect($this->columns)
            ->map(function ($column) use ($context) {
                return $column->context($context);
            })
            ->toArray();

        $this->filters = collect($this->filters)
            ->map(function ($filter) use ($context) {
                return $filter->context($context);
            })
            ->toArray();

        return $this;
    }

    public function disableFiltering()
    {
        $this->filterable = false;

        return $this;
    }

    public function disablePagination()
    {
        $this->pagination = false;

        return $this;
    }

    public function disableSearching()
    {
        $this->searchable = false;

        return $this;
    }

    public function disableSorting()
    {
        $this->sortable = false;

        return $this;
    }

    public function filterable($filterable)
    {
        $this->filterable = $filterable;

        return $this;
    }

    public function getVisibleColumns()
    {
        $columns = collect($this->columns)
            ->filter(fn ($column) => ! $column->hidden)
            ->toArray();

        return $columns;
    }

    public function getVisibleFilters()
    {
        $filters = collect($this->filters)
            ->filter(fn ($filter) => ! $filter->hidden)
            ->toArray();

        return $filters;
    }

    public function hasPagination()
    {
        return $this->pagination;
    }

    public function isFilterable()
    {
        return $this->filterable && count($this->filters);
    }

    public function isSearchable()
    {
        return $this->searchable && collect($this->columns)
                ->filter(fn ($column) => $column->searchable)
                ->count();
    }

    public function isSortable()
    {
        return $this->sortable && collect($this->columns)
                ->filter(fn ($column) => $column->sortable)
                ->count();
    }

    public function pagination($enabled)
    {
        $this->pagination = $enabled;

        return $this;
    }

    public function primaryRecordAction($action)
    {
        $this->columns = collect($this->columns)
            ->map(function ($column) use ($action) {
                return $column->action($action);
            })
            ->toArray();

        return $this;
    }

    public function primaryRecordUrl($url)
    {
        $this->columns = collect($this->columns)
            ->map(function ($column) use ($url) {
                return $column->url($url);
            })
            ->toArray();

        return $this;
    }

    public function recordActions($actions)
    {
        $this->recordActions = Arr::collapse($this->recordActions, $actions);

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
