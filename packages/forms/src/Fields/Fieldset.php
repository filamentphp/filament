<?php

namespace Filament\Forms\Fields;

use Illuminate\Support\Str;

class Fieldset extends Field
{
    public $columns = 2;

    public static function make($label = null)
    {
        $fieldset = (new static())->label($label);

        if ($label) $fieldset = $fieldset->id(Str::slug($label));

        return $fieldset;
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
