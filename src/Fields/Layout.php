<?php

namespace Filament\Fields;

use Filament\View\Components\Fields;

class Layout extends BaseField
{
    public $columns = 1;

    public $fields;

    public static function columns($columns)
    {
        return new static($columns);
    }

    public function fields($fields = [])
    {
        $this->fields = $fields;

        return $this;
    }

    public function getFields()
    {
        return new Fields($this->fields, $this->columns);
    }
}
