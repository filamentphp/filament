<?php

namespace Filament;

use Illuminate\Support\Str;

abstract class Resource
{
    public $actions = [];

    public $defaultAction = 'index';

    public $icon = 'heroicon-o-database';

    public $label;

    public $model;

    public $slug;

    public $sort;

    public function getAction($name = null)
    {
        if (! $name) return static::getDefaultAction();

        return static::getActions()[$name] ?? null;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function getDefaultAction()
    {
        $actionClass = static::getAction($this->defaultAction);

        if ((new $actionClass)->hasRouteParameter) return;

        return $actionClass;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getLabel()
    {
        if ($this->label) return $this->label;

        return (string) Str::of($this->model)
            ->kebab()
            ->replace('-', ' ');
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getSlug()
    {
        if ($this->slug) return $this->slug;

        return (string) Str::of(class_basename($this->model))->kebab()->plural();
    }

    public function getSort()
    {
        return $this->sort;
    }
}
