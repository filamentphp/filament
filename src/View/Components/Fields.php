<?php

namespace Filament\View\Components;

use Illuminate\View\Component;

class Fields extends Component
{
    public $columns = 1;

    public $fields = [];

    public function __construct($fields = [], $columns = 1)
    {
        $this->columns = $columns;
        $this->fields = $fields;
    }

    public function getRules()
    {
        $rules = [];

        foreach ($this->fields as $field) {
            if ($field->getRules()) $rules[$field->model] = $field->getRules();

            $rules = array_merge($rules, $field->getFields()->getRules());
        }

        return $rules;
    }

    public function render()
    {
        return view('filament::components.fields');
    }
}
