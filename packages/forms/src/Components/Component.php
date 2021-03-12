<?php

namespace Filament\Forms\Components;

use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Component
{
    use Tappable;

    protected $columnSpan = 1;

    protected $configurationQueue = [];

    protected $hidden = false;

    protected $form;

    protected $id;

    protected $label;

    protected $parent;

    protected $schema = [];

    protected $view;

    protected function setUp()
    {
        //
    }

    public function columnSpan($span)
    {
        $this->configure(function () use ($span) {
            $this->columnSpan = $span;
        });

        return $this;
    }

    public function configure($callback = null)
    {
        if ($callback === null) {
            foreach ($this->configurationQueue as $callback) {
                $callback();
            }

            $this->configurationQueue = [];

            return;
        }

        if ($this->form) {
            $callback();
        } else {
            $this->configurationQueue[] = $callback;
        }

        return $this;
    }

    public function except($contexts, $callback = null)
    {
        $this->configure(function () use ($contexts, $callback) {
            if (! is_array($contexts)) $contexts = [$contexts];

            if (! $callback) {
                $this->hidden();

                $callback = fn ($component) => $component->visible();
            }

            if (! $this->getContext() || in_array($this->getContext(), $contexts)) return $this;

            $callback($this);
        });

        return $this;
    }

    public function form($form)
    {
        $this->form = $form;

        $this->schema(
            collect($this->getSchema())
                ->map(fn ($component) => $component->form($this->getForm()))
                ->toArray(),
        );

        $this->configure();

        return $this;
    }

    public function hidden()
    {
        $this->configure(function () {
            $this->hidden = true;
        });

        return $this;
    }

    public function getColumnSpan()
    {
        return $this->columnSpan;
    }

    public function getContext()
    {
        return $this->getForm()->getContext();
    }

    public function getDefaultValues()
    {
        if ($this->isHidden()) return [];

        $values = [];

        if ($this instanceof Field) {
            $values[$this->getName()] = $this->getDefaultValue();
        }

        $values = array_merge($values, $this->getSubform()->getDefaultValues());

        return $values;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getId()
    {
        return (string) Str::of($this->id)
            ->prepend(
                $this->getContext() ?
                    (string) Str::of($this->getContext())
                        ->replace('\\', '-')
                        ->lower()
                        ->append('-') :
                    '',
            );
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getModel()
    {
        return $this->getForm()->getModel();
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getRecord()
    {
        return $this->getForm()->getRecord();
    }

    public function getRules($field = null)
    {
        if ($field !== null) {
            return $this->rules[$field] ?? null;
        }

        if ($this->isHidden()) return [];

        $rules = $this instanceof Field ? $this->rules : [];

        foreach ($rules as $field => $conditions) {
            $rules[$field] = $this->transformConditions($conditions);
        }

        foreach ($this->getSubform()->getRules() as $field => $conditions) {
            $rules[$field] = array_merge($rules[$field] ?? [], $this->transformConditions($conditions));
        }

        return $rules;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getSubform()
    {
        return Form::make()
            ->context($this->getContext())
            ->model($this->getModel())
            ->record($this->getRecord())
            ->schema($this->getSchema());
    }

    public function getValidationAttributes()
    {
        if ($this->isHidden()) return [];

        $attributes = [];

        if ($this instanceof Field) {
            if ($this->validationAttribute !== null) {
                $attributes[$this->getName()] = Str::lower(__($this->getLabel()));
            } else {
                $attributes[$this->getName()] = __($this->validationAttribute);
            }
        }

        $attributes = array_merge($attributes, $this->getSubform()->getValidationAttributes());

        return $attributes;
    }

    public function getView()
    {
        return $this->view;
    }

    public function id($id)
    {
        $this->configure(function () use ($id) {
            $this->id = $id;
        });

        return $this;
    }

    public function isHidden()
    {
        return $this->hidden;
    }

    public function label($label)
    {
        $this->configure(function () use ($label) {
            $this->label = $label;
        });

        return $this;
    }

    public function only($contexts, $callback = null)
    {
        $this->configure(function () use ($callback, $contexts) {
            if (! is_array($contexts)) $contexts = [$contexts];

            if (! $callback) {
                $this->hidden();

                $callback = fn ($component) => $component->visible();
            }

            if (! in_array($this->getContext(), $contexts)) return $this;

            $callback($this);
        });

        return $this;
    }

    public function parent($component)
    {
        $this->parent = $component;

        return $this;
    }

    public function schema($schema)
    {
        $this->schema = collect(value($schema))
            ->map(fn ($component) => $component->parent($this))
            ->toArray();

        return $this;
    }

    public function view($view)
    {
        $this->configure(function () use ($view) {
            $this->view = $view;
        });

        return $this;
    }

    public function visible()
    {
        $this->configure(function () {
            $this->hidden = false;
        });

        return $this;
    }

    public function when($condition, $callback = null)
    {
        $this->configure(function () use ($callback, $condition) {
            if (! $callback) {
                $this->hidden();

                $callback = fn ($component) => $component->visible();
            }

            if ($this->getRecord() === null) return $this;

            try {
                $shouldExecuteCallback = $condition($this->getRecord());
            } catch (\Exception $exception) {
                $shouldExecuteCallback = false;
            }

            if ($shouldExecuteCallback) {
                $callback($this, $this->getRecord());
            }
        });

        return $this;
    }

    public function render()
    {
        if ($this->isHidden()) return;

        $view = $this->getView() ?? 'forms::components.' . Str::of(class_basename(static::class))->kebab();

        return view($view, ['formComponent' => $this]);
    }

    protected function transformConditions($conditions)
    {
        return collect($conditions)
            ->map(function ($condition) {
                if (! is_string($condition)) return $condition;

                return (string) Str::of($condition)->replace('{{record}}', $this->getRecord() instanceof Model ? $this->getRecord()->getKey() : '');
            })
            ->toArray();
    }
}
