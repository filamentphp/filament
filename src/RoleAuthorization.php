<?php

namespace Filament;

class RoleAuthorization
{
    public $exceptActions = [];

    public $onlyActions = [];

    public $role;

    public $type;

    public function __construct($role, $type = 'deny')
    {
        $this->role = $role;
        $this->type = in_array($type, ['allow', 'deny']) ? $type : 'deny';
    }

    public function except($actions)
    {
        if (! is_array($actions)) $actions = [$actions];

        $this->exceptActions = array_merge($this->exceptActions, $actions);

        return $this;
    }

    public function only($actions)
    {
        if (! is_array($actions)) $actions = [$actions];

        $this->onlyActions = array_merge($this->onlyActions, $actions);

        return $this;
    }
}
