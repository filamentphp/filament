<?php

namespace Filament\Resources;

use Illuminate\Support\Facades\Auth;

class AuthorizationManager
{
    public $authorizations = [];

    public $mode = 'deny';

    public $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;

        $this->authorize($resource::authorization());
    }

    public function authorize($authorizations)
    {
        if (! $authorizations) return;

        if (! is_array($authorizations)) $authorizations = [$authorizations];

        if (! count($this->authorizations)) $this->mode = $authorizations[0]->mode;

        $authorizations = array_filter($authorizations, fn ($authorization) => $authorization->mode === $this->mode);

        $this->authorizations = array_merge($this->authorizations, $authorizations);

        return $this;
    }

    public function can($page, $user = null)
    {
        if (! $user) $user = Auth::guard('filament')->user();

        return true;

//        if (! count($this->authorizations)) return true;
//
//        if (! $this->resource::router()->hasRoute($route)) return $this->mode === 'deny';
//
//        return collect($this->authorizations)
//            ->contains(function ($authorization) use ($route, $user) {
//                if (! $user->hasRole($authorization->role)) return false;
//
//                if (in_array($route, $authorization->onlyRoutes)) return $this->mode === 'allow';
//
//                if (in_array($route, $authorization->exceptRoutes)) return $this->mode === 'deny';
//
//                return $this->mode === 'deny';
//            }) || $this->mode === 'deny';
    }
}
