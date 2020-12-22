<?php

namespace Filament\Fields;

class Fieldset extends BaseField {
    public $legend;
    public $fields;
    public $class;
    public $info;
    
    public function __construct($legend)
    {
        $this->legend = $legend;
    }

    /**
     * @return static
     */
    public static function make(string $legend): self
    {
        return new static($legend);
    }

    /**
     * @return static
     */
    public function fields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @return static
     */
    public function class(string $class): self
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return static
     */
    public function info(string $info): self
    {
        $this->info = $info;
        return $this;
    }
}