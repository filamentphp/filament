<?php

namespace Filament;

use Illuminate\Support\Facades\Auth;

class ResourceAuthorizationManager
{
    public $authorizations = [];

    public $resource;

    public $type = 'deny';

    public function __construct($resource)
    {
        $this->resource = $resource;

        $this->authorize($resource::authorization());
    }

    public function authorize($authorizations)
    {
        if (! $authorizations) return;

        if (! is_array($authorizations)) $authorizations = [$authorizations];

        if (! count($this->authorizations)) $this->type = $authorizations[0]->type;

        $authorizations = array_filter($authorizations, fn ($authorization) => $authorization->type === $this->type);

        $this->authorizations = array_merge($this->authorizations, $authorizations);

        return $this;
    }

    public function can($route, $user = null)
    {
        if (! $user) $user = Auth::guard('filament')->user();

        if (! count($this->authorizations)) return true;

        if (! $this->resource::router()->hasRoute($route)) return $this->type === 'deny';

        return collect($this->authorizations)
                ->contains(function ($authorization) use ($route, $user) {
                    if (! $user->hasRole($authorization->role)) return false;

                    if (in_array($route, $authorization->onlyRoutes)) return $this->type === 'allow';

                    if (in_array($route, $authorization->exceptRoutes)) return $this->type === 'deny';

                    return $this->type === 'deny';
                }) || $this->type === 'deny';
    }
}
