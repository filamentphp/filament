<?php

namespace Filament;

class FilamentManager
{
    public $pages = [];

    public $resources = [];

    public $roles = [];

    public $widgets = [];

    public function getPages()
    {
        return $this->pages;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getWidgets()
    {
        return $this->widgets;
    }

    public function registerPage($page)
    {
        $this->pages = array_merge($this->pages, [$page]);
    }

    public function registerResource($resource)
    {
        $this->resources = array_merge($this->resources, [$resource]);
    }

    public function registerRole($role)
    {
        $this->roles = array_merge($this->roles, [$role]);
    }

    public function registerWidget($widget)
    {
        $this->widgets = array_merge($this->widgets, [$widget]);
    }
}
