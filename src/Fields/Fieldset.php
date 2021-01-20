<?php

namespace Filament\Fields;

class Fieldset extends BaseField
{
    public $columns;

    public $legend;

    public $fields;

    public function __construct($legend)
    {
        $this->legend = $legend;
    }

    public static function make(string $legend)
    {
        return new static($legend);
    }

    public function columns(string $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }
}
