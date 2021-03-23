<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Field extends Component
{
    protected $bindingAttribute = 'wire:model.defer';

    protected $defaultValue = null;

    protected $extraAttributes = [];

    protected $helpMessage;

    protected $hint;

    protected $isDisabled = false;

    protected $isRequired = false;

    protected $name;

    protected $rules = [];

    protected $validationAttribute;

    public function __construct($name)
    {
        $this->name($name);

        $this->setUp();
    }

    public function addRules($rules)
    {
        $this->configure(function () use ($rules) {
            if (! is_array($rules)) {
                $rules = [$this->getName() => $rules];
            }

            foreach ($rules as $field => $conditionsToAdd) {
                if (is_numeric($field)) {
                    $field = $this->getName();
                }

                if (! is_array($conditionsToAdd)) {
                    $conditionsToAdd = explode('|', $conditionsToAdd);
                }

                $this->rules[$field] = collect($this->getRules($field) ?? [])
                    ->filter(function ($originalCondition) use ($conditionsToAdd) {
                        if (! is_string($originalCondition)) {
                            return true;
                        }

                        $conditionsToAdd = collect($conditionsToAdd);

                        if ($conditionsToAdd->contains($originalCondition)) {
                            return false;
                        }

                        if (! Str::of($originalCondition)->contains(':')) {
                            return true;
                        }

                        $originalConditionType = (string) Str::of($originalCondition)->before(':');

                        return ! $conditionsToAdd->contains(function ($conditionToAdd) use ($originalConditionType) {
                            return $originalConditionType === (string) Str::of($conditionToAdd)->before(':');
                        });
                    })
                    ->push(...$conditionsToAdd)
                    ->toArray();
            }
        });

        return $this;
    }

    public function bindingAttribute($bindingAttribute)
    {
        $this->configure(function () use ($bindingAttribute) {
            $this->bindingAttribute = $bindingAttribute;
        });

        return $this;
    }

    public function default($value)
    {
        $this->configure(function () use ($value) {
            $this->defaultValue = $value;
        });

        return $this;
    }

    public function dependable()
    {
        $this->configure(function () {
            $this->bindingAttribute('wire:model');
        });

        return $this;
    }

    public function disabled($disabled = true)
    {
        $this->configure(function () use ($disabled) {
            $this->isDisabled = $disabled;
        });

        return $this;
    }

    public function enabled()
    {
        $this->configure(function () {
            $this->isDisabled = false;
        });

        return $this;
    }

    public function extraAttributes($attributes)
    {
        $this->configure(function () use ($attributes) {
            $this->extraAttributes = $attributes;
        });

        return $this;
    }

    public function getBindingAttribute()
    {
        return $this->bindingAttribute;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function getExtraAttributes()
    {
        return $this->extraAttributes;
    }

    public function getHelpMessage()
    {
        return $this->helpMessage;
    }

    public function getHint()
    {
        return $this->hint;
    }

    public function getId()
    {
        if ($this->id === null) {
            return (string) Str::of($this->getName())
                ->replace('.', '-')
                ->slug();
        }

        return parent::getId();
    }

    public function getLabel()
    {
        if ($this->label === null) {
            return (string) Str::of($this->getName())
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValidationAttribute()
    {
        if ($this->validationAttribute === null) {
            return Str::lower($this->getLabel());
        }

        return $this->validationAttribute;
    }

    public function getValue()
    {
        return $this->getLivewire()->getPropertyValue($this->getName());
    }

    public function helpMessage($message)
    {
        $this->configure(function () use ($message) {
            $this->helpMessage = $message;
        });

        return $this;
    }

    public function hint($hint)
    {
        $this->configure(function () use ($hint) {
            $this->hint = $hint;
        });

        return $this;
    }

    public function isDisabled()
    {
        return $this->isDisabled;
    }

    public function isRequired()
    {
        return $this->isRequired;
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function name($name)
    {
        $this->name = $name;

        $this->addRules([$this->getName() => ['nullable']]);
    }

    public function nullable()
    {
        $this->configure(function () {
            $this->required = false;

            $this->removeRules([$this->getName() => ['required']]);
            $this->addRules([$this->getName() => ['nullable']]);
        });

        return $this;
    }

    public function removeRules($rules)
    {
        $this->configure(function () use ($rules) {
            if (! is_array($rules)) {
                $rules = [$this->getName() => $rules];
            }

            foreach ($rules as $field => $conditionsToRemove) {
                if (is_numeric($field)) {
                    $field = $this->getName();
                }

                if (! is_array($conditionsToRemove)) {
                    $conditionsToRemove = explode('|', $conditionsToRemove);
                }

                if (empty($conditionsToRemove)) {
                    unset($this->rules[$field]);

                    return;
                }

                $this->rules[$field] = collect($this->getRules($field) ?? [])
                    ->filter(function ($originalCondition) use ($conditionsToRemove) {
                        if (! is_string($originalCondition)) {
                            return true;
                        }

                        $conditionsToRemove = collect($conditionsToRemove);

                        if ($conditionsToRemove->contains($originalCondition)) {
                            return false;
                        }

                        if (! Str::of($originalCondition)->contains(':')) {
                            return true;
                        }

                        $originalConditionType = (string) Str::of($originalCondition)->before(':');

                        return ! $conditionsToRemove->contains(function ($conditionToRemove) use ($originalConditionType) {
                            return $originalConditionType === (string) Str::of($conditionToRemove)->before(':');
                        });
                    })
                    ->toArray();
            }
        });

        return $this;
    }

    public function required()
    {
        $this->configure(function () {
            $this->isRequired = true;

            $this->removeRules([$this->getName() => ['nullable']]);
            $this->addRules([$this->getName() => ['required']]);
        });

        return $this;
    }

    public function requiredWith($field)
    {
        $this->configure(function () use ($field) {
            $this->isRequired = false;

            $this->removeRules([$this->getName() => ['nullable', 'required']]);
            $this->addRules([$this->getName() => ["required_with:$field"]]);
        });

        return $this;
    }

    public function rules($conditions)
    {
        $this->configure(function () use ($conditions) {
            $this->addRules([$this->getName() => $conditions]);
        });

        return $this;
    }

    public function validationAttribute($attribute)
    {
        $this->configure(function () use ($attribute) {
            $this->validationAttribute = $attribute;
        });

        return $this;
    }
}
