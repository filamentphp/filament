<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class BaseField {
    public $value;
    public $id;
    public $hint;
    public $help;
    protected $view;

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    public function id($id)
    {
        $this->id = $id;
        return $this;
    }

    public function hint($hint)
    {
        $this->hint = $hint;
        return $this;
    }

    public function help($help)
    {
        $this->help = $help;
        return $this;
    }

    public function view($view)
    {
        $this->view = $view;
        return $this;
    }

    public function render()
    {        
        $view = $this->view ?? 'filament::fields.'.Str::kebab(class_basename(get_called_class()));
        $data = get_object_vars($this);

        return view($view, $data);
    }
}