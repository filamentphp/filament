<?php

namespace Filament\Pages;

use Illuminate\Support\Facades\Auth;

class AuthorizationManager
{
    public $authorizations = [];

    public $mode = 'deny';

    public $page;

    public function __construct($page)
    {
        $this->page = $page;

        $this->authorize($page::authorization());
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

    public function can($user = null)
    {
        if (! $user) $user = Auth::guard('filament')->user();

        return true;

//        if (! count($this->authorizations)) return true;
//
//        $roleAuthorized = collect($this->authorizations)
//            ->contains(function ($authorization) use ($user) {
//                if (! $user->hasRole($authorization->role)) return false;
//
//                $this->mode === 'deny';
//
//                return true;
//            });
//
//        if (! $roleAuthorized) {
//            $this->mode === 'deny';
//
//            return true;
//        }
    }
}
