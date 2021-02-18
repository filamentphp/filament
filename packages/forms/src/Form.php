<?php

namespace Filament\Forms;

class Form
{
    public $columns = 1;

    public $fields = [];

    public $rules = [];

    public $submitMethod = 'submit';

    public function __construct($fields = [])
    {
        $this->fields = $fields;
    }

    public static function make($fields = [])
    {
        return new static($fields);
    }

    public function context($context)
    {
        $this->fields = collect($this->fields)
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

        foreach ($this->fields as $field) {
            $defaults = array_merge($defaults, $field->getDefaults());
        }

        return $defaults;
    }

    public function getFields()
    {
        $fields = $this->fields;

        foreach ($this->fields as $field) {
            $fields = array_merge($fields, $field->getForm()->getFields());
        }

        return $fields;
    }

    public function getRules()
    {
        $rules = $this->rules;

        foreach ($this->fields as $field) {
            foreach ($field->getRules() as $name => $conditions) {
                $rules[$name] = array_merge($rules[$name] ?? [], $conditions);
            }
        }

        return $rules;
    }

    public function getValidationAttributes()
    {
        $attributes = [];

        foreach ($this->fields as $field) {
            $attributes = array_merge($attributes, $field->getValidationAttributes());
        }

        return $attributes;
    }

    public function model($model)
    {
        $this->fields = collect($this->fields)
            ->map(function ($field) use ($model) {
                return $field->model($model);
            })
            ->toArray();

        return $this;
    }

    public function record($record)
    {
        $this->fields = collect($this->fields)
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

    public function submitMethod($method)
    {
        $this->submitMethod = $method;

        return $this;
    }
}
