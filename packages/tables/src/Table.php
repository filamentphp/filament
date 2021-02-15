<?php

namespace Filament\Tables;

class Table
{
    public $columns = [];

    public function __construct($columns = [], $context = null)
    {
        $this->columns = $columns;

        if ($context) $this->passContextToColumns($context);
    }

    public function passContextToColumns($context = null)
    {
        if ($context) {
            $this->columns = collect($this->columns)
                ->map(function ($field) use ($context) {
                    return $field->context($context);
                })
                ->toArray();
        }

        return $this->columns;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getVisibleColumns()
    {
        $columns = collect($this->getColumns())
            ->filter(fn($column) => ! $column->hidden)
            ->toArray();

        return $columns;
    }
}
