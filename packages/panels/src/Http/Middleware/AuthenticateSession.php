<?php

namespace Filament\Http\Middleware;

use Filament\Facades\Filament;
use Illuminate\Session\Middleware\AuthenticateSession as Middleware;

class AuthenticateSession extends Middleware
{
    protected function redirectTo($request): ?string
    {
        return Filament::getLoginUrl();
    }
}
