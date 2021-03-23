<?php

namespace Filament\Forms;

use Illuminate\Support\Traits\Tappable;

class Form
{
    use Tappable;

    protected $columns = 1;

    protected $livewire;

    protected $model;

    protected $rules = [];

    protected $schema = [];

    protected $submitMethod = 'submit';

    protected $validationAttributes = [];

    public function __construct($livewire)
    {
        $this->livewire($livewire);
    }

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public static function for($livewire)
    {
        return new static($livewire);
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getContext()
    {
        return get_class($this->getLivewire());
    }

    public function getDefaultValues()
    {
        $defaults = [];

        foreach ($this->getSchema() as $component) {
            $defaults = array_merge($defaults, $component->getDefaultValues());
        }

        return $defaults;
    }

    public function getFlatSchema()
    {
        $schema = $this->getSchema();

        foreach ($this->schema as $component) {
            $schema = array_merge($schema, $component->getSubform()->getFlatSchema());
        }

        return $schema;
    }

    public function getLivewire()
    {
        return $this->livewire;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getRules()
    {
        $rules = $this->rules;

        foreach ($this->getSchema() as $component) {
            foreach ($component->getRules() as $name => $conditions) {
                $rules[$name] = array_merge($rules[$name] ?? [], $conditions);
            }
        }

        return $rules;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getSubmitMethod()
    {
        return $this->submitMethod;
    }

    public function getValidationAttributes()
    {
        $attributes = $this->validationAttributes;

        foreach ($this->getSchema() as $component) {
            $attributes = array_merge($attributes, $component->getValidationAttributes());
        }

        return $attributes;
    }

    public function livewire($component)
    {
        $this->livewire = $component;

        return $this;
    }

    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    public function rules($rules)
    {
        $this->rules = value($rules);

        return $this;
    }

    public function schema($schema)
    {
        $this->schema = collect(value($schema))
            ->map(function ($component) {
                return $component->form($this);
            })
            ->toArray();

        return $this;
    }

    public function submitMethod($method)
    {
        $this->submitMethod = $method;

        return $this;
    }

    public function validationAttributes($attributes)
    {
        $this->validationAttributes = $attributes;

        return $this;
    }
}
