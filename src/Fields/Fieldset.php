<?php

namespace Filament\Fields;

class Fieldset extends BaseField {
    public $legend;
    public $fields;
    public $class;
    
    public function __construct($legend)
    {
        $this->legend = $legend;
    }

    public static function legend($legend)
    {
        return new static($legend);
    }

    public function fields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function class($class)
    {
        $this->class = $class;
        return $this;
    }
}