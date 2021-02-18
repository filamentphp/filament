<?php

namespace Filament;

class RoleAuthorization
{
    public $exceptPages = [];

    public $mode;

    public $onlyPages = [];

    public $role;

    public function __construct($role, $mode = 'deny')
    {
        $this->mode = in_array($mode, ['allow', 'deny']) ? $mode : 'deny';
        $this->role = $role;
    }

    public function except($pages)
    {
        if (! is_array($pages)) $pages = [$pages];

        $this->exceptPages = array_merge($this->exceptPages, $pages);

        return $this;
    }

    public function only($pages)
    {
        if (! is_array($pages)) $pages = [$pages];

        $this->onlyPages = array_merge($this->onlyPages, $pages);

        return $this;
    }
}
