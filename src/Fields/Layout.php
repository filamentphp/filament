<?php

namespace Filament\Fields;

class Layout extends BaseField {
    public $class;
    public $fields;
    
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * @return static
     */
    public static function make(string $class): self
    {
        return new static($class);
    }

    /**
     * @return static
     */
    public function fields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }
}