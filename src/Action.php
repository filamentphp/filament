<?php

namespace Filament;

use Illuminate\Support\Str;
use Livewire\Component;

abstract class Action extends Component
{
    public $hasRouteParameter = true;

    public $resource;

    public $title;

    public function getClassName()
    {
        return class_basename(static::class);
    }

    public function getModel()
    {
        $resourceClass = $this->getResource();

        return (new $resourceClass)->getModel();
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getTitle()
    {
        if ($this->title) return $this->title;

        return (string) Str::of($this->getClassName())
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }
}
