<?php

namespace Filament\Fields;

class Layout extends BaseField {
    public $class;
    public $fields;
    
    public function __construct($class)
    {
        $this->class = $class;
    }

    public static function make($class)
    {
        return new static($class);
    }

    public function fields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }
}