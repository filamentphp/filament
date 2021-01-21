<?php

namespace Filament\Fields;

class Fieldset extends BaseField
{
    public $columns = 1;

    public $legend;

    public $fields;

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function fields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    public static function make($legend)
    {
        return new static($legend);
    }
}
