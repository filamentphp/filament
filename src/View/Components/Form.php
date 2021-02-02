<?php

namespace Filament\View\Components;

use Illuminate\View\Component;

class Form extends Component
{
    public $columns = 1;

    public $fields = [];

    public $hooks = [];

    public $rules = [];

    public function __construct($fields = [], $record = null)
    {
        $this->fields = $fields;

        if ($record) $this->passRecordToFields($record);
    }

    public function callHooks($event, $action)
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

    public function columns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function getHooks()
    {
        $hooks = $this->hooks;

        collect($this->fields)
            ->each(function ($field) use (&$hooks) {
                collect($field->getHooks())
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

    public function hooks($hooks)
    {
        $this->hooks = $hooks;
    }

    public function passRecordToFields($record = null)
    {
        if (! $record) return $this->fields;

        return $this->fields = collect($this->fields)
            ->map(function ($field) use ($record) {
                return $field
                    ->record($record)
                    ->fields($field->getFields()->passRecordToFields($record));
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
