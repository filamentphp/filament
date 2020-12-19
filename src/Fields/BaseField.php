<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class BaseField {
    protected $enabled = true;
    protected $view;

    public function enabled(bool $enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function view($view)
    {
        $this->view = $view;
        return $this;
    }

    public function render()
    {        
        if (! $this->enabled) {
            return;
        }

        $view = $this->view ?? 'filament::fields.'.Str::kebab(class_basename(get_called_class()));

        return view($view, get_object_vars($this));
    }
}