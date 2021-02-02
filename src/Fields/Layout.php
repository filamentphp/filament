<?php

namespace Filament\Fields;

use Filament\View\Components\Form;

class Layout extends Field
{
    public $columns = 1;

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function getFields()
    {
        return parent::getFields()->columns($this->columns);
    }

    public static function make($fields)
    {
        return (new static())->fields($fields);
    }
}
