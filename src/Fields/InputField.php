<?php

namespace Filament\Fields;

class InputField extends Field
{
    public $errorKey;

    public $extraAttributes = [];

    public $help;

    public $hint;

    public $maxLength;

    public $minLength;

    public $modelDirective = 'wire:model.defer';

    public $required = false;

    public function confirmed($confirmationFieldName = null)
    {
        if ($confirmationFieldName === null) $confirmationFieldName = "{$this->name}Confirmation";

        $this->addRules([$confirmationFieldName => ["same:$this->name"]]);

        return $this;
    }

    public function errorKey($errorKey)
    {
        $this->errorKey = $errorKey;

        return $this;
    }

    public function extraAttributes($attributes)
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function help($help)
    {
        $this->help = $help;

        return $this;
    }

    public function hint($hint)
    {
        $this->hint = $hint;

        return $this;
    }

    public function image()
    {
        $this->addRules([$this->name => ['image']]);

        return $this;
    }

    public function label($label)
    {
        $this->label = $label;

        return $this;
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function maxLength($value)
    {
        $this->maxLength = $value;

        $this->addRules([$this->name => ["max:$value"]]);

        return $this;
    }

    public function maxSize($value)
    {
        $this->addRules([$this->name => ["max:$value"]]);

        return $this;
    }

    public function minLength($value)
    {
        $this->minLength = $value;

        $this->addRules([$this->name => ["min:$value"]]);

        return $this;
    }

    public function minSize($value)
    {
        $this->addRules([$this->name => ["min:$value"]]);

        return $this;
    }

    public function modelDirective($modelDirective)
    {
        $this->modelDirective = $modelDirective;

        return $this;
    }

    public function nullable()
    {
        $this->required = false;

        $this->removeRules([$this->name => ['required']]);
        $this->addRules([$this->name => ['nullable']]);

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

    public function same($field)
    {
        $this->addRules([$this->name => ["same:$field"]]);

        return $this;
    }

    public function unique($table, $column = null, $exceptCurrentRecord = false)
    {
        $rule = "unique:$table,$column";
        if ($exceptCurrentRecord) $rule .= ',{{record}}';

        $this->addRules([$this->name => [$rule]]);

        return $this;
    }
}
