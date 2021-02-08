<?php

namespace Filament;

class Form
{
    public $columns = 1;

    public $fields = [];

    public $hooks = [];

    public $rules = [];

    public function __construct($fields = [], $context = null, $record = null)
    {
        $this->fields = $fields;

        if ($context) $this->passContextToFields($context);

        if ($record) $this->passRecordToFields($record);
    }

    public function passContextToFields($context = null)
    {
        if (! $context) return $this->fields;

        return $this->fields = collect($this->fields)
            ->map(function ($field) use ($context) {
                return $field
                    ->context($context)
                    ->fields($field->getForm()->passContextToFields($context));
            })
            ->toArray();
    }

    public function passRecordToFields($record = null)
    {
        if (! $record) return $this->fields;

        return $this->fields = collect($this->fields)
            ->map(function ($field) use ($record) {
                return $field
                    ->record($record)
                    ->fields($field->getForm()->passRecordToFields($record));
            })
            ->toArray();
    }

    public function callHooks($action, $event)
    {
        $hooks = $this->getHooks();

        if (! array_key_exists($event, $hooks)) return $action;

        collect($hooks[$event])
            ->filter(fn ($hook) => is_callable($hook))
            ->each(function ($hook) use (&$action) {
                $action = $hook($action);
            });

        return $action;
    }

    public function getHooks()
    {
        $hooks = $this->hooks;

        foreach ($this->fields as $field) {
            foreach ($field->getHooks as $event => $callbacks) {
                $hooks[$event] = array_merge($hooks[$event] ?? [], $callbacks);
            }
        }

        return $hooks;
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

    public function hooks($hooks)
    {
        $this->hooks = $hooks;
    }

    public function rules($rules)
    {
        $this->rules = $rules;
    }

    public function render()
    {
        return view('filament::components.fields');
    }
}
