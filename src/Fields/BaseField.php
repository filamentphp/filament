<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class BaseField {
    protected $view;

    public function view($view)
    {
        $this->view = $view;
        return $this;
    }

    public function render()
    {        
        $view = $this->view ?? 'filament::fields.'.Str::kebab(class_basename(get_called_class()));

        return view($view, get_object_vars($this));
    }
}