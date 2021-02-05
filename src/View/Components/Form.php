<?php

namespace Filament\View\Components;

use Illuminate\View\Component;

class Form extends Component
{
    public $actionHooks = [];

    public $columns = 1;

    public $fields = [];

    public $rules = [];

    public function __construct($fields = [], $context = null, $record = null)
    {
        $this->fields = $fields;

        if ($context) $this->passContextToFields($context);

        if ($record) $this->passRecordToFields($record);
    }

    public function actionHooks($hooks)
    {
        $this->actionHooks = $hooks;
    }

    public function callActionHooks($action, $event)
    {
        $hooks = $this->getActionHooks();

        if (! array_key_exists($event, $hooks)) return $action;

        collect($hooks[$event])
            ->filter(fn ($hook) => is_callable($hook))
            ->each(function ($hook) use (&$action) {
                $action = $hook($action);
            });

        return $action;
    }

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function getActionHooks()
    {
        $hooks = $this->actionHooks;

        collect($this->fields)
            ->each(function ($field) use (&$hooks) {
                collect($field->getActionHooks())
                    ->each(function ($callbacks, $event) use (&$hooks) {
                        $hooks[$event] = array_merge($hooks[$event] ?? [], $callbacks);
                    });
            });

        return $hooks;
    }

    public function getRules()
    {
        $rules = $this->rules;

        collect($this->fields)
            ->each(function ($field) use (&$rules) {
                collect($field->getRules())
                    ->each(function ($conditions, $field) use (&$rules) {
                        $rules[$field] = array_merge($rules[$field] ?? [], $conditions);
                    });
            });

        return $rules;
    }

    public function getValidationAttributes()
    {
        $attributes = [];

        collect($this->fields)
            ->each(function ($field) use (&$attributes) {
                collect($field->getValidationAttributes())
                    ->each(function ($label, $name) use (&$attributes) {
                        $attributes[$name] = $label;
                    });
            });

        return $attributes;
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

    public function rules($rules)
    {
        $this->rules = $rules;
    }

    public function render()
    {
        return view('filament::components.fields');
    }
}
