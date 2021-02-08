<?php

namespace Filament\Fields;

use Filament\View\Components\Form;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Field
{
    use Tappable;

    public $fields = [];

    public $hidden = false;

    public $hooks = [];

    public $parentField;

    protected $context;

    protected $pendingExcludedContextModifications = [];

    protected $pendingIncludedContextModifications = [];

    protected $record;

    protected $view;

    public function context($context)
    {
        $this->context = $context;

        if (array_key_exists($this->context, $this->pendingIncludedContextModifications)) {
            foreach ($this->pendingIncludedContextModifications[$this->context] as $callback) {
                $callback($this);
            }
        }

        $this->pendingIncludedContextModifications = [];

        collect($this->pendingExcludedContextModifications)
            ->filter(fn ($callbacks, $context) => $context !== $this->context)
            ->each(function ($callbacks) {
                foreach ($callbacks as $callback) {
                    $callback($this);
                }
            });

        $this->pendingExcludedContextModifications = [];

        return $this;
    }

    public function dd()
    {
        dd($this);

        return $this;
    }

    public function except($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($field) => $field->visible();
        }

        if (! $this->context) {
            foreach ($contexts as $context) {
                if (! array_key_exists($context, $this->pendingExcludedContextModifications)) {
                    $this->pendingExcludedContextModifications[$context] = [];
                }

                $this->pendingExcludedContextModifications[$context][] = $callback;
            }

            return $this;
        }

        if (in_array($this->context, $contexts)) return $this;

        $callback($this);

        return $this;
    }

    public function hidden()
    {
        $this->hidden = true;

        return $this;
    }

    public function fields($fields)
    {
        $this->fields = collect($fields)
            ->map(fn ($field) => $field->parentField($this))
            ->toArray();

        return $this;
    }

    public function getDefaults()
    {
        if ($this->hidden) return [];

        $defaults = [];

        if (property_exists($this, 'name') && property_exists($this, 'default')) {
            $defaults[$this->name] = $this->default;
        }

        $defaults = array_merge($defaults, $this->getForm()->getDefaults());

        return $defaults;
    }

    public function getForm()
    {
        return new Form($this->fields, $this->context, $this->record);
    }

    public function getHooks()
    {
        $hooks = $this->hooks;

        foreach ($this->getForm()->getHooks() as $event => $callbacks) {
            $hooks[$event] = array_merge($hooks[$event] ?? [], $callbacks);
        }

        return $hooks;
    }

    public function getRules()
    {
        if ($this->hidden) return [];

        $rules = property_exists($this, 'rules') ? $this->rules : [];

        foreach ($this->getForm()->getRules() as $field => $conditions) {
            $conditions = collect($conditions)
                ->map(function ($condition) {
                    if (! is_string($condition)) return $condition;

                    return (string) Str::of($condition)->replace('{{record}}', $this->record ? $this->record->getKey() : '');
                })
                ->toArray();

            $rules[$field] = array_merge($rules[$field] ?? [], $conditions);
        }

        return $rules;
    }

    public function getValidationAttributes()
    {
        if ($this->hidden) return [];

        $attributes = [];

        if (property_exists($this, 'name') && property_exists($this, 'label')) {
            $label = Str::of($this->label)->lower();

            $attributes[$this->name] = $label;
        }

        $attributes = array_merge($attributes, $this->getForm()->getValidationAttributes());

        return $attributes;
    }

    public function only($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($field) => $field->visible();
        }

        if (! $this->context) {
            foreach ($contexts as $context) {
                if (! array_key_exists($context, $this->pendingIncludedContextModifications)) {
                    $this->pendingIncludedContextModifications[$context] = [];
                }

                $this->pendingIncludedContextModifications[$context][] = $callback;
            }

            return $this;
        }

        if (! in_array($this->context, $contexts)) return $this;

        $callback($this);

        return $this;
    }

    public function parentField($field)
    {
        $this->parentField = $field;

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

    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    public function visible()
    {
        $this->hidden = false;

        return $this;
    }

    public function render()
    {
        if ($this->hidden) return;

        $view = $this->view ?? 'filament::fields.' . Str::of(class_basename(static::class))->kebab();

        return view($view, ['field' => $this]);
    }
}
