<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class BaseField {
    public $model;
    public $id;
    public $help;
    protected $enabled = true;
    protected $view;

    public function __construct($model)
    {
        $this->model = $model;
        $this->id = Str::slug($model);
    }
   
    /**
     * @return static
     */
    public static function make(string $model): self
    {
        return new static($model);
    }

    /**
     * @return static
     */
    public function id($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return static
     */
    public function help(string $help): self
    {
        $this->help = $help;
        return $this;
    }

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