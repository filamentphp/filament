<?php

namespace Filament\Fields;

use Filament\Traits\FieldConcerns;
use Illuminate\Support\Str;

class InputField extends Field
{
    use FieldConcerns\CanHaveId;
    use FieldConcerns\CanHaveLabel;

    public $default = '';

    public $errorKey;

    public $extraAttributes = [];

    public $helpMessage;

    public $hint;

    public $name;

    public $nameAttribute = 'wire:model.defer';

    public $required = false;

    public $rules = [];

    public function __construct($name)
    {
        $this->name($name);
    }

    public function errorKey($errorKey)
    {
        $this->errorKey = $errorKey;

        return $this;
    }

    public function rules($conditions)
    {
        $this->rules = [$this->name => $conditions];

        return $this;
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function default($default)
    {
        $this->default = $default;

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

    public function name($name)
    {
        $this->name = $name;

        $this->errorKey($this->name);

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

        $this->rules(['nullable']);
    }

    public function nameAttribute($nameAttribute)
    {
        $this->nameAttribute = $nameAttribute;

        return $this;
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
        foreach ($rules as $field => $conditionsToRemove) {
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
        }

        return $this;
    }

    public function addRules($rules)
    {
        foreach ($rules as $field => $conditionsToAdd) {
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
}
