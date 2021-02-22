<?php

namespace Filament\Forms;

class Form
{
    public $columns = 1;

    public $rules = [];

    public $schema = [];

    public $submitMethod = 'submit';

    public static function make()
    {
        return new static();
    }

    public function context($context)
    {
        $this->schema = collect($this->schema)
            ->map(function ($field) use ($context) {
                return $field->context($context);
            })
            ->toArray();

        return $this;
    }

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function getDefaults()
    {
        $defaults = [];

        foreach ($this->schema as $field) {
            $defaults = array_merge($defaults, $field->getDefaults());
        }

        return $defaults;
    }

    public function getSchema()
    {
        $schema = $this->schema;

        foreach ($this->schema as $component) {
            $schema = array_merge($schema, $component->getForm()->getSchema());
        }

        return $schema;
    }

    public function getRules()
    {
        $rules = $this->rules;

        foreach ($this->schema as $field) {
            foreach ($field->getRules() as $name => $conditions) {
                $rules[$name] = array_merge($rules[$name] ?? [], $conditions);
            }
        }

        return $rules;
    }

    public function getValidationAttributes()
    {
        $attributes = [];

        foreach ($this->schema as $field) {
            $attributes = array_merge($attributes, $field->getValidationAttributes());
        }

        return $attributes;
    }

    public function model($model)
    {
        $this->schema = collect($this->schema)
            ->map(function ($field) use ($model) {
                return $field->model($model);
            })
            ->toArray();

        return $this;
    }

    public function record($record)
    {
        $this->schema = collect($this->schema)
            ->map(function ($field) use ($record) {
                return $field->record($record);
            })
            ->toArray();

        return $this;
    }

    public function rules($rules)
    {
        $this->rules = $rules;

        return $this;
    }

    public function schema($schema)
    {
        $this->schema = $schema;

        return $this;
    }

    public function submitMethod($method)
    {
        $this->submitMethod = $method;

        return $this;
    }
}
