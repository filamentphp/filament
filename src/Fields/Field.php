<?php

namespace Filament\Fields;

use Filament\View\Components\Fields;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Field
{
    use Tappable;

    public $enabled = true;

    public $fields = [];

    public $hooks = [];

    public $id;

    public $name;

    public $record;

    public $rules = [];

    protected $view;

    public function __construct($name = null)
    {
        $this->name = $name;

        if ($this->name !== null) {
            $this->id(Str::slug($this->name));
            $this->rules = [$this->name => ['nullable']];
        }

        return $this;
    }

    public function addRules($rules)
    {
        collect($rules)
            ->each(function ($conditionsToAdd, $field) {
                if (! is_array($conditionsToAdd)) $conditionsToAdd = explode('|', $conditionsToAdd);

                $this->rules[$field] = collect($this->rules[$field] ?? [])
                    ->filter(function ($originalCondition) use ($conditionsToAdd) {
                        if (! is_string($originalCondition)) return true;

                        $conditionsToAdd = collect($conditionsToAdd);

                        if ($conditionsToAdd->contains($originalCondition)) return false;

                        if (! Str::of($originalCondition)->contains(':')) return true;

                        $originalConditionType = (string) Str::of($originalCondition)->before(':');

                        return ! $conditionsToAdd->contains(function ($conditionToAdd) use ($originalConditionType) {
                            return $originalConditionType === (string) Str::of($conditionToAdd)->before(':');
                        });
                    })
                    ->push(...$conditionsToAdd)
                    ->toArray();
            });

        return $this;
    }

    public function dd()
    {
        dd($this);

        return $this;
    }

    public function disable()
    {
        $this->enabled = false;

        return $this;
    }

    public function enable()
    {
        $this->enabled = true;

        return $this;
    }

    public function fields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    public function getFields()
    {
        return new Fields($this->fields, $this->record);
    }

    public function getHooks()
    {
        $hooks = $this->hooks;

        collect($this->getFields()->getHooks())
            ->each(function ($callbacks, $event) use (&$hooks) {
                $hooks[$event] = array_merge($hooks[$event] ?? [], $callbacks);
            });

        return $hooks;
    }

    public function getRules()
    {
        $rules = $this->rules;

        collect($this->getFields()->getRules())
            ->each(function ($conditions, $field) use (&$rules) {
                $conditions = collect($conditions)
                    ->map(function ($condition) {
                        if (! is_string($condition)) return $condition;

                        return (string) Str::of($condition)->replace('{{record}}', $this->record->getKey());
                    })
                    ->toArray();

                $rules[$field] = array_merge($rules[$field] ?? [], $conditions);
            });

        return $rules;
    }

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function record($record)
    {
        $this->record = $record;

        return $this;
    }

    public function registerHook($event, $callback)
    {
        if (! array_key_exists($event, $this->hooks)) $this->hooks[$event] = [];

        $this->hooks[$event][] = $callback;

        return $this;
    }

    public function removeRules($rules)
    {
        collect($rules)
            ->each(function ($conditionsToRemove, $field) {
                if (! is_array($conditionsToRemove)) $conditionsToRemove = explode('|', $conditionsToRemove);

                if (empty($conditionsToRemove)) {
                    unset($this->rules[$field]);

                    return;
                }

                $this->rules[$field] = collect($this->rules[$field] ?? [])
                    ->filter(function ($originalCondition) use ($conditionsToRemove) {
                        if (! is_string($originalCondition)) return true;

                        $conditionsToRemove = collect($conditionsToRemove);

                        if ($conditionsToRemove->contains($originalCondition)) return false;

                        if (! Str::of($originalCondition)->contains(':')) return true;

                        $originalConditionType = (string) Str::of($originalCondition)->before(':');

                        return ! $conditionsToRemove->contains(function ($conditionToRemove) use ($originalConditionType) {
                            return $originalConditionType === (string) Str::of($conditionToRemove)->before(':');
                        });
                    })
                    ->toArray();
            });

        return $this;
    }

    public function rules($rules)
    {
        $this->rules = [$this->name => $rules];

        return $this;
    }

    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    public function render()
    {
        if (! $this->enabled) {
            return;
        }

        $view = $this->view ?? 'filament::fields.' . Str::of(class_basename(static::class))->kebab();

        return view($view, ['field' => $this]);
    }
}
