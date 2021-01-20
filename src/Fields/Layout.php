<?php

namespace Filament\Fields;

class Layout extends BaseField
{
    public $columns;

    public $fields;

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    public static function columns($columns)
    {
        return new static($columns);
    }

    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }
}
