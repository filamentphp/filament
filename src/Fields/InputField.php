<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class InputField extends Field
{
    public $default = '';

    public $errorKey;

    public $modelDirective = 'wire:model.defer';

    public $required = false;

    public $rules = [];

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

    public function errorKey($errorKey)
    {
        $this->errorKey = $errorKey;

        return $this;
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function modelDirective($modelDirective)
    {
        $this->modelDirective = $modelDirective;

        return $this;
    }

    public function name($name)
    {
        parent::name($name);

        $this->errorKey($this->name);
        $this->rules(['nullable']);
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

    public function required()
    {
        $this->required = true;

        $this->removeRules([$this->name => ['nullable']]);
        $this->addRules([$this->name => ['required']]);

        return $this;
    }

    public function rules($conditions)
    {
        $this->rules = [$this->name => $conditions];

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
