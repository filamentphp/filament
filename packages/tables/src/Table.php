<?php

namespace Filament\Tables;

class Table
{
    public $columns = [];

    public $recordUrl;

    public $sortColumn;

    public $sortDirection = 'asc';

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

    public function recordUrl($url)
    {
        $this->recordUrl = $url;

        return $this;
    }

    public function sortColumn($column)
    {
        $this->sortColumn = $column;

        return $this;
    }

    public function sortDirection($direction)
    {
        $this->sortDirection = $direction;

        return $this;
    }
}
