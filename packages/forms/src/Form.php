<?php

namespace Filament\Forms;

use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Traits\Tappable;

class Form
{
    use Tappable;

    protected $columns = 1;

    protected $livewire;

    protected $parent;

    protected $rules = [];

    protected $schema = [];

    protected $validationAttributes = [];

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public static function extend($form)
    {
        return (new static())->parent($form);
    }

    public static function for($livewire)
    {
        return (new static())->livewire($livewire);
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
        return $this->livewire ?? $this->getParent()->getLivewire();
    }

    public function getParent()
    {
        return $this->parent;
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

    public function parent($form)
    {
        $this->parent = $form;

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

    public function validationAttributes($attributes)
    {
        $this->validationAttributes = $attributes;

        return $this;
    }
}
