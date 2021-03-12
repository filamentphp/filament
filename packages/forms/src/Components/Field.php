<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Field extends Component
{
    protected $bindingAttribute = 'wire:model.defer';

    protected $default = null;

    protected $disabled = false;

    protected $extraAttributes = [];

    protected $helpMessage;

    protected $hint;

    protected $name;

    protected $required = false;

    protected $rules = [];

    protected $validationAttribute;

    public function __construct($name)
    {
        $this->name($name);

        $this->setUp();
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function bindingAttribute($bindingAttribute)
    {
        $this->bindingAttribute = $bindingAttribute;

        return $this;
    }

    public function dependable()
    {
        $this->bindingAttribute('wire:model');

        return $this;
    }

    public function disabled()
    {
        $this->disabled = true;

        return $this;
    }

    public function enabled()
    {
        $this->disabled = false;

        return $this;
    }

    public function name($name)
    {
        $this->name = $name;

        $this->id(
            (string) Str::of($this->name)
                ->replace('.', '-')
                ->slug(),
        );

        $this->label(
            (string) Str::of($this->name)
                ->afterLast('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst(),
        );

        $this->addRules([$this->name => ['nullable']]);
    }

    public function rules($conditions)
    {
        $this->addRules([$this->name => $conditions]);

        return $this;
    }

    public function default($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function extraAttributes($attributes)
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function helpMessage($message)
    {
        $this->helpMessage = $message;

        return $this;
    }

    public function hint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function getName()
    {
        return $this->name;
    }

    public function nullable()
    {
        $this->required = false;

        $this->removeRules([$this->name => ['required']]);
        $this->addRules([$this->name => ['nullable']]);

        return $this;
    }

    public function removeRules($rules)
    {
        if (! is_array($rules)) {
            $rules = [$this->name => $rules];
        }

        foreach ($rules as $field => $conditionsToRemove) {
            if (is_numeric($field)) {
                $field = $this->name;
            }

            if (! is_array($conditionsToRemove)) $conditionsToRemove = explode('|', $conditionsToRemove);

            if (empty($conditionsToRemove)) {
                unset($this->rules[$field]);

                return;
            }

            $this->rules[$field] = collect($this->rules[$field] ?? [])
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

        return $this;
    }

    public function addRules($rules)
    {
        if (! is_array($rules)) {
            $rules = [$this->name => $rules];
        }

        foreach ($rules as $field => $conditionsToAdd) {
            if (is_numeric($field)) {
                $field = $this->name;
            }

            if (! is_array($conditionsToAdd)) {
                $conditionsToAdd = explode('|', $conditionsToAdd);
            }

            $this->rules[$field] = collect($this->rules[$field] ?? [])
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

        return $this;
    }

    public function required()
    {
        $this->required = true;

        $this->removeRules([$this->name => ['nullable']]);
        $this->addRules([$this->name => ['required']]);

        return $this;
    }

    public function requiredWith($field)
    {
        $this->required = false;

        $this->removeRules([$this->name => ['nullable', 'required']]);
        $this->addRules([$this->name => ["required_with:$field"]]);

        return $this;
    }

    public function validationAttribute($attribute)
    {
        $this->validationAttribute = $attribute;

        return $this;
    }
}
