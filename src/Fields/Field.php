<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class Field extends BaseField {
    public $name;
    public $label;
    public $model;
    public $modelDirective;
    public $extraAttributes;
    protected $view;
    
    public function __construct($name)
    {
        $this->name = $name;
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function label($label)
    {
        $this->label = $label;
        return $this;
    }

    public function model($model, $directive = 'wire:model.defer')
    {
        $this->model = $model;
        $this->modelDirective = $directive;
        return $this;
    }

    public function extraAttributes(array $attributes)
    {
        $this->extraAttributes = $attributes;
        return $this;
    }
}