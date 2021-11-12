<?php

namespace Filament\Http\Middleware;

use Exception;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards): void
    {
        $guardName = config('filament.auth.guard');
        $guard = $this->auth->guard($guardName);

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse($guardName);

        $user = $this->auth->user();
        $userClass = $user::class;

        if (! $user instanceof FilamentUser) {
            throw new Exception("Class [{$userClass}] does not implement the \Filament\Models\Contracts\FilamentUser interface.");
        }

        abort_unless(
            $user->canAccessFilament(),
            404,
        );
    }

    protected function redirectTo($request): string
    {
        return route('filament.auth.login');
    }
}
