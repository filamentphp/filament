<?php

namespace Filament\Fields;

class Field extends BaseField {
    public $model;
    public $modelDirective = 'wire:model.defer';
    public $value;
    public $id;
    public $label;
    public $hint;
    public $help;
    public $extraAttributes = [];
    
    public function __construct($model)
    {
        $this->model = $this->id = $model;
    }

    /**
     * @return static
     */
    public static function make(string $model): self
    {
        return new static($model);
    }

    /**
     * @return static
     */
    public function modelDirective(string $modelDirective): self
    {
        $this->modelDirective = $modelDirective;
        return $this;
    }

    /**
     * @return static
     */
    public function value($value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return static
     */
    public function id($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return static
     */
    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return static
     *
     * @param array|null|string $hint
     */
    public function hint($hint): self
    {
        $this->hint = $hint;
        return $this;
    }

    /**
     * @return static
     */
    public function help(string $help): self
    {
        $this->help = $help;
        return $this;
    }

    /**
     * @return static
     */
    public function extraAttributes(array $attributes): self
    {
        $this->extraAttributes = $attributes;
        return $this;
    }
}