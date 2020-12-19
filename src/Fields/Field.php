<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class Field extends BaseField {
    public $model;
    public $value;
    public $label;
    public $modelDirective = 'wire:model.defer';
    public $extraAttributes = [];
    protected $view;
    
    public function __construct($model)
    {
        $this->model = $model;
    }

    public static function make($model)
    {
        return new static($model);
    }

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    public function modelDirective($modelDirective)
    {
        $this->modelDirective = $modelDirective;
        return $this;
    }

    public function extraAttributes(array $attributes)
    {
        $this->extraAttributes = $attributes;
        return $this;
    }
}