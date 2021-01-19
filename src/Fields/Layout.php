<?php

namespace Filament\Fields;

class Layout extends BaseField
{
    public $class = 'space-y-6';

    public $fields;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public static function make(?string $class = null)
    {
        return new static($class);
    }

    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }
}
