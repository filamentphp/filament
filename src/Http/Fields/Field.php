<?php

namespace Filament\Http\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Field
{
    protected $field_type;
    protected $name;
    protected $label;
    protected $key;
    protected $id;
    protected $value;
    protected $class;
    protected $group;
    protected $options;
    protected $default;
    protected $autocomplete;
    protected $placeholder;
    protected $help;
    protected $rules;
    protected $required;
    protected $view;
    protected $multiple;
    protected $disabled;

    public function __construct($name, $label = null, $key = null)
    {
        $this->field_type = Str::of(class_basename(get_called_class()))->kebab();
        $this->name = $name;
        $this->label = is_null($label) ? $this->formatLabel($name) : $label;
        $this->key = $key ?? 'form_data.'.$this->name;
        $this->id = Str::slug($this->key);
    }

    public static function make($name, $label = null, $key = null)
    {
        return new static($name, $label, $key);
    }

    public function __get($property)
    {
        return $this->$property;
    }

    public function disabled($is_disabled = true)
    {
        $this->disabled = (bool) $is_disabled;
        return $this;
    }

    public function multiple()
    {
        $this->multiple = true;
        return $this;
    }
    
    public function options($options)
    {
        $this->options = Arr::isAssoc($options) ? array_flip($options) : array_combine($options, $options);
        return $this;
    }

    public function default($default)
    {
        $this->default = $default;
        return $this;
    }

    public function value($value)
    {
        $this->value = old($this->key, $value);
        return $this;
    }

    public function class($class)
    {
        $this->class = $class;
        return $this;
    }

    public function group($group)
    {
        $this->group = $group;
        return $this;
    }

    public function autocomplete($autocomplete)
    {
        $this->autocomplete = $autocomplete;
        return $this;
    }

    public function placeholder($placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function help($help)
    {
        $this->help = $help;
        return $this;
    }

    public function rules($rules)
    {
        $this->rules = $rules;
        $this->required = is_array($rules) ? in_array('required', $rules) : stristr($rules, 'required');
        return $this;
    }

    public function view($view)
    {
        $this->view = $view;
        return $this;
    }

    public function errorMessage($message)
    {
        return str_replace('form data.', '', $message);
    }

    public function render()
    {
        $view = $this->view ?? "filament::fields.{$this->field_type}";

        return view($view, ['field' => $this]);
    }

    protected function formatLabel($value)
    {
        return Str::of($value)
            ->replaceMatches('/[\-_]/', ' ')
            ->title()
            ->__toString();
    }
}
