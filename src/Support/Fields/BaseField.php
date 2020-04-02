<?php

namespace Filament\Support\Fields;

use Illuminate\Support\Arr;

class BaseField
{
    protected $name;
    protected $type;
    protected $value;
    protected $group;
    protected $input_type;
    protected $textarea_rows;
    protected $options;
    protected $default;
    protected $autocomplete;
    protected $placeholder;
    protected $help;
    protected $rules;
    protected $required = false;
    protected $view;

    public function __get($property)
    {
        return $this->$property;
    }

    public function input($type = 'text')
    {
        $this->type = 'input';
        $this->input_type = $type;
        return $this;
    }

    public function textarea($rows = 2)
    {
        $this->type = 'textarea';
        $this->textarea_rows = $rows;
        return $this;
    }

    public function select($options = [])
    {
        $this->type = 'select';
        $this->options($options);
        return $this;
    }

    public function checkbox()
    {
        $this->type = 'checkbox';
        return $this;
    }

    public function checkboxes($options = [])
    {
        $this->type = 'checkboxes';
        $this->options($options);
        return $this;
    }

    public function radio($options = [])
    {
        $this->type = 'radio';
        $this->options($options);
        return $this;
    }

    protected function options($options)
    {
        $this->options = Arr::isAssoc($options) ? array_flip($options) : array_combine($options, $options);
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

    public function getView()
    {
        return $this->view ?? 'filament::fields.' . $this->type;
    }
}
