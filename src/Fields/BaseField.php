<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class BaseField
{
    public $id;

    public $model;

    public $modelDirective = 'wire:model.defer';

    public $value;

    protected $enabled = true;

    protected $view;

    public function __construct($model)
    {
        $this->model = $model;
        $this->id = Str::slug($model);
    }

    public static function make(string $model)
    {
        return new static($model);
    }

    public function enabled(bool $enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function model(string $model)
    {
        $this->model = $model;

        return $this;
    }

    public function modelDirective(string $modelDirective)
    {
        $this->modelDirective = $modelDirective;

        return $this;
    }

    public function value($value)
    {
        $this->value = $value;

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

        $view = $this->view ?? 'filament::fields.' . Str::kebab(class_basename(get_called_class()));

        return view($view, ['field' => $this]);
    }
}
