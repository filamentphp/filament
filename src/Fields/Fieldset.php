<?php

namespace Filament\Fields;

class Fieldset extends BaseField
{
    public $class;

    public $legend;

    public $fields;

    public function __construct($legend)
    {
        $this->legend = $legend;
    }

    public static function make(string $legend)
    {
        return new static($legend);
    }

    public function class(string $class)
    {
        $this->class = $class;

        return $this;
    }

    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }
}
