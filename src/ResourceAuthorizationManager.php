<?php

namespace Filament;

use Illuminate\Support\Facades\Auth;

class ResourceAuthorizationManager
{
    public $resource;

    public $roleAuthorizations = [];

    public $type = 'deny';

    public function __construct($resource)
    {
        $this->resource = $resource;

        $this->authorize((new $resource)->authorization());
    }

    public function authorize($roleAuthorizations)
    {
        if (! $roleAuthorizations) return;

        if (! is_array($roleAuthorizations)) $roleAuthorizations = [$roleAuthorizations];

        if (! count($this->roleAuthorizations)) $this->type = $roleAuthorizations[0]->type;

        $roleAuthorizations = array_filter($roleAuthorizations, fn ($roleAuthorization) => $roleAuthorization->type === $this->type);

        $this->roleAuthorizations = array_merge($this->roleAuthorizations, $roleAuthorizations);

        return $this;
    }

    public function can($action)
    {
        if (! count($this->roleAuthorizations)) return true;

        $actions = $this->resource::actions();

        if (! in_array($action, array_values($actions))) return $this->type === 'deny';

        return collect($this->roleAuthorizations)
            ->contains(function ($roleAuthorization) use ($action) {
                if (! Auth::guard('filament')->user()->hasRole($roleAuthorization->role)) return false;

                if (in_array($action, $roleAuthorization->onlyActions)) return $this->type === 'allow';

                if (in_array($action, $roleAuthorization->exceptActions)) return $this->type === 'deny';

                return $this->type === 'deny';
            }) || $this->type === 'deny';
    }
}
