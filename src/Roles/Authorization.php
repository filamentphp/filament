<?php

namespace Filament\Roles;

class Authorization
{
    public $exceptActions = [];

    public $mode;

    public $onlyActions = [];

    public $role;

    public function __construct($role, $mode = 'deny')
    {
        $this->mode = in_array($mode, ['allow', 'deny']) ? $mode : 'deny';
        $this->role = $role;
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
