<?php

namespace Filament\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('filament')->check()) {
            return $this->auth->shouldUse('filament');
        }

        $this->unauthenticated($request, $guards);
    }

    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('filament.auth.login');
        }
    }
}
