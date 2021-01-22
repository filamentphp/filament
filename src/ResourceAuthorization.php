<?php

namespace Filament;

class ResourceAuthorization
{
    public $exceptRoutes = [];

    public $onlyRoutes = [];

    public $role;

    public $type;

    public function __construct($role, $type = 'deny')
    {
        $this->role = $role;
        $this->type = in_array($type, ['allow', 'deny']) ? $type : 'deny';
    }

    public function except($routes)
    {
        if (! is_array($routes)) $routes = [$routes];

        $this->exceptRoutes = array_merge($this->exceptRoutes, $routes);

        return $this;
    }

    public function only($routes)
    {
        if (! is_array($routes)) $routes = [$routes];

        $this->onlyRoutes = array_merge($this->onlyRoutes, $routes);

        return $this;
    }
}
