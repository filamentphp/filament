<?php

namespace Filament\Fields;

class Layout extends BaseField {
    public $columns;
    public $fields;
    
    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return static
     */
    public static function columns($columns): self
    {
        return new static($columns);
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