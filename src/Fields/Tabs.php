<?php

namespace Filament\Fields;

class Tabs extends Field
{
    public $label;

    public $tabs = [];

    public function __construct($label)
    {
        $this->label = $label;
    }

    public static function make(string $label)
    {
        return new static($label);
    }

    public function tab(string $label, array $fields)
    {
        $this->tabs[$label] = $fields;

        return $this;
    }
}
