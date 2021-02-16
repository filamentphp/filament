<?php

namespace Filament\Tables;

class Table
{
    public $columns = [];

    public $pagination = true;

    public $recordUrl;

    public $searchable = true;

    public $sortable = true;

    public function __construct($columns = [])
    {
        $this->columns = $columns;
    }

    public static function make($columns = [])
    {
        return new static($columns);
    }

    public function context($context)
    {
        $this->columns = collect($this->columns)
            ->map(function ($column) use ($context) {
                return $column->context($context);
            })
            ->toArray();

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
            ->filter(fn($column) => ! $column->hidden)
            ->toArray();

        return $columns;
    }

    public function pagination($enabled)
    {
        $this->pagination = $enabled;

        return $this;
    }

    public function recordUrl($url)
    {
        $this->recordUrl = $url;

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
