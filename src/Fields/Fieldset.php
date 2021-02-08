<?php

namespace Filament\Fields;

use Filament\FieldConcerns;

class Fieldset extends Field
{
    use FieldConcerns\HasLabel;

    public $columns = 2;

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
