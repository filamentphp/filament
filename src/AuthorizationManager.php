<?php

namespace Filament;

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
        $user = Filament::auth()->user();

        if (
            ! count($this->authorizations) ||
            $user->isFilamentAdmin()
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
