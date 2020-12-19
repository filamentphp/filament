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
        $this->model = $model;
    }

    public static function make($model)
    {
        return new static($model);
    }

    public function modelDirective($modelDirective)
    {
        $this->modelDirective = $modelDirective;
        return $this;
    }

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    public function id($id)
    {
        $this->id = $id;
        return $this;
    }

    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    public function hint($hint)
    {
        $this->hint = $hint;
        return $this;
    }

    public function help($help)
    {
        $this->help = $help;
        return $this;
    }

    public function extraAttributes(array $attributes)
    {
        $this->extraAttributes = $attributes;
        return $this;
    }
}