<?php

namespace Filament;

class RoleAuthorization
{
    public $exceptRoutes = [];

    public $mode;

    public $onlyRoutes = [];

    public $role;

    public function __construct($role, $mode = 'deny')
    {
        $this->mode = in_array($mode, ['allow', 'deny']) ? $mode : 'deny';
        $this->role = $role;
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
