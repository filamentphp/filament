<?php

namespace Filament\Fields;

class Fieldset extends BaseField {
    public $legend;
    public $fields;
    public $columns;
    
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
    public function columns(string $columns): self
    {
        $this->columns = $columns;
        return $this;
    }
}