<?php

namespace Filament\Fields;

class Fieldset extends Field
{
    public $columns = 1;

    public $legend;

    public static function make($legend, $fields)
    {
        return (new static())->fields($fields)->legend($legend);
    }

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function getFields()
    {
        return parent::getFields()->columns($this->columns);
    }

    public function legend($legend)
    {
        $this->legend = $legend;

        return $this;
    }
}
