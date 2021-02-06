<?php

namespace Filament\Fields;

class Fieldset extends Field
{
    public $columns = 1;

    public static function make($label = null)
    {
        return (new static())->label($label);
    }

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function getForm()
    {
        return parent::getForm()->columns($this->columns);
    }
}
