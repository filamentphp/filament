<?php

namespace Filament\Fields;

class Tabs extends BaseField {
    public $label;
    public $tabs = [];

    public function __construct($label)
    {
        $this->label = $label;
    }

    /**
     * @return static
     */
    public static function label(string $label): self
    {
        return new static($label);
    }

    /**
     * @return static
     */
    public function tab(string $label, array $fields): self
    {
        $this->tabs[$label] = $fields;
        return $this;
    }
}