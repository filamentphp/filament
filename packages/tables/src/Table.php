<?php

namespace Filament\Tables;

class Table
{
    public $columns = [];

    public $filterable = true;

    public $pagination = true;

    public $recordButtonLabel = 'tables::table.record.button.label';

    public $recordUrl;

    public $searchable = true;

    public $sortable = true;

    public function __construct($columns = [], $filters = [])
    {
        $this->columns = $columns;
        $this->filters = $filters;
    }

    public static function make($columns = [], $filters = [])
    {
        return new static($columns, $filters);
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

    public function getRecordUrl($record = null)
    {
        if (is_callable($this->recordUrl)) {
            $callback = $this->recordUrl;

            return $callback($record);
        }

        return $this->recordUrl;
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

    public function recordButtonLabel($label)
    {
        $this->recordButtonLabel = $label;

        return $this;
    }

    public function recordUrl($url)
    {
        $this->recordUrl = $url;

        $this->columns = collect($this->columns)
            ->map(function ($column) {
                if ($column->primary && ! $column->url) {
                    $column->url($this->recordUrl);
                }

                return $column;
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
