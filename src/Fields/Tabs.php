<?php

namespace Filament\Fields;

class Tabs extends BaseField {
    public $label;
    public $tabs = [];

    public function __construct($label)
    {
        $this->label = $label;
    }

    public static function label($label)
    {
        return new static($label);
    }

    public function tab($label, array $fields)
    {
        $this->tabs[$label] = $fields;
        return $this;
    }
}