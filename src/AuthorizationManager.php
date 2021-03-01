<?php

namespace Filament;

use Illuminate\Support\Facades\Auth;

class AuthorizationManager
{
    public $authorizations = [];

    public $mode = 'deny';

    public $target;

    public function __construct($target)
    {
        $this->target = $target;

        $this->authorize($this->target::authorization());
    }

    public function authorize($authorizations)
    {
        if (! $authorizations) return;

        if (! is_array($authorizations)) {
            $authorizations = [$authorizations];
        }

        if (! count($this->authorizations)) {
            $this->mode = $authorizations[0]->mode;
        }

        $authorizations = array_filter(
            $authorizations,
            fn ($authorization) => $authorization->mode === $this->mode,
        );

        $this->authorizations = array_merge($this->authorizations, $authorizations);

        return $this;
    }

    public function can($action = null)
    {
        $user = Auth::guard('filament')->user();

        if (
            ! count($this->authorizations) ||
            $user->is_admin
        ) {
            return true;
        }

        if ($this->mode === 'allow') {
            foreach ($this->authorizations as $authorization) {
                if ($user->hasRole($authorization->role)) {
                    return true;
                }
            }

            return false;
        }

        if ($this->mode === 'deny') {
            foreach ($this->authorizations as $authorization) {
                if ($user->hasRole($authorization->role)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
