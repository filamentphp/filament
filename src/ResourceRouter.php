<?php

namespace Filament;

class ResourceRouter
{
    public $resource;

    public $routes = [];

    public function __construct($resource)
    {
        $this->resource = $resource;

        $this->register($resource::routes());
    }

    public function register($routes)
    {
        if (! $routes) return;

        if (! is_array($routes)) $routes = [$routes];

        $this->routes = array_merge($this->routes, $routes);

        return $this;
    }

    public function getIndexRoute()
    {
        return collect($this->routes)
            ->first(fn ($route) => $route->name === 'index' || $route->uri === '');
    }

    public function hasRoute($name)
    {
        return collect($this->routes)
            ->contains(fn ($route) => $route->name === $name);
    }
}
