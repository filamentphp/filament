<?php

namespace Filament\Fields;

class Fieldset extends Field
{
    public $columns = 1;

    public $legend;

    public static function make($legend = null)
    {
        return (new static())->legend($legend);
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

    public function legend($legend = null)
    {
        $this->legend = $legend;

        return $this;
    }
}
