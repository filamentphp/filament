<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class BaseField {
    protected $enabled = true;
    protected $view;

    /**
     * @return static
     */
    public function enabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return static
     */
    public function view($view): self
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @return \Illuminate\View\View|null
     */
    public function render()
    {        
        if (! $this->enabled) {
            return;
        }

        $view = $this->view ?? 'filament::fields.'.Str::kebab(class_basename(get_called_class()));

        return view($view, get_object_vars($this));
    }
}