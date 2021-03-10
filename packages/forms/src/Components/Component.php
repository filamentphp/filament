<?php

namespace Filament\Forms\Components;

use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Component
{
    use Tappable;

    public $context;

    public $columnSpan = 1;

    public $hidden = false;

    public $id;

    public $label;

    public $model;

    public $parent;

    public $schema = [];

    public $record;

    public $view;

    protected $pendingExcludedContextModifications = [];

    protected $pendingIncludedContextModifications = [];

    protected $pendingModelModifications = [];

    protected $pendingRecordModifications = [];

    protected function setUp()
    {
        //
    }

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

        $this->schema($this->getSubform()->context($this->context)->schema);

        return $this;
    }

    public function columnSpan($span)
    {
        $this->columnSpan = $span;

        return $this;
    }

    public function except($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($component) => $component->visible();
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

    public function schema($schema)
    {
        $this->schema = collect(value($schema))
            ->map(fn ($component) => $component->parent($this))
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

        $defaults = array_merge($defaults, $this->getSubform()->getDefaults());

        return $defaults;
    }

    public function getId()
    {
        return (string) Str::of($this->context)->replace('\\', '_')->lower()->append('_'.$this->id);
    }

    public function getSubform()
    {
        $form = Form::make()->schema($this->schema);

        if ($this->context) {
            $form->context($this->context);
        }

        if ($this->record) {
            $form->record($this->record);
        }

        return $form;
    }

    public function getRules()
    {
        if ($this->hidden) return [];

        $rules = property_exists($this, 'rules') ? $this->rules : [];

        foreach ($rules as $field => $conditions) {
            $rules[$field] = $this->transformConditions($conditions);
        }

        foreach ($this->getSubform()->getRules() as $field => $conditions) {
            $rules[$field] = array_merge($rules[$field] ?? [], $this->transformConditions($conditions));
        }

        return $rules;
    }

    public function getValidationAttributes()
    {
        if ($this->hidden) return [];

        $attributes = [];

        if (property_exists($this, 'name')) {
            if (property_exists($this, 'label')) {
                $label = Str::lower(__($this->label));

                $attributes[$this->name] = $label;
            }

            if (property_exists($this, 'validationAttribute') && $this->validationAttribute !== null) {
                $attributes[$this->name] = __($this->validationAttribute);
            }
        }

        $attributes = array_merge($attributes, $this->getSubform()->getValidationAttributes());

        return $attributes;
    }

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public function model($model)
    {
        $this->model = $model;

        foreach ($this->pendingModelModifications as $callback) {
            $callback($this);
        }

        $this->pendingModelModifications = [];

        $this->schema($this->getSubform()->model($this->model)->schema);

        return $this;
    }

    public function only($contexts, $callback = null)
    {
        if (! is_array($contexts)) $contexts = [$contexts];

        if (! $callback) {
            $this->hidden();

            $callback = fn ($component) => $component->visible();
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

    public function parent($component)
    {
        $this->parent = $component;

        return $this;
    }

    public function record($record)
    {
        $this->record = $record;

        if ($this->record instanceof Model) {
            $this->model(get_class($this->record));
        }

        $this->schema($this->getSubform()->record($this->record)->schema);

        foreach ($this->pendingRecordModifications as [$condition, $callback]) {
            try {
                $shouldExecuteCallback = $condition($this->record);
            } catch (\Exception $exception) {
                $shouldExecuteCallback = false;
            }

            if ($shouldExecuteCallback) {
                $callback($this, $this->record);
            }
        }

        $this->pendingRecordModifications = [];

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

    public function when($condition, $callback = null)
    {
        if (! $callback) {
            $this->hidden();

            $callback = fn ($component) => $component->visible();
        }

        if ($this->record === null) {
            $this->pendingRecordModifications[] = [$condition, $callback];

            return $this;
        }

        try {
            $shouldExecuteCallback = $condition($this->record);
        } catch (\Exception $exception) {
            $shouldExecuteCallback = false;
        }

        if ($shouldExecuteCallback) {
            $callback($this, $this->record);
        }

        return $this;
    }

    public function render()
    {
        if ($this->hidden) return;

        $view = $this->view ?? 'forms::components.' . Str::of(class_basename(static::class))->kebab();

        return view($view, ['formComponent' => $this]);
    }

    protected function transformConditions($conditions)
    {
        return collect($conditions)
            ->map(function ($condition) {
                if (! is_string($condition)) return $condition;

                return (string) Str::of($condition)->replace('{{record}}', $this->record instanceof Model ? $this->record->getKey() : '');
            })
            ->toArray();
    }
}
