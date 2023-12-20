<?php

namespace Filament\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;

class LogoutController
{
    public function __invoke(): LogoutResponse
    {
        Filament::auth()->logout();

        $logins = array_filter(
            array_keys(session()->all()),
            fn (string $key): bool => str($key)->startsWith('login_'),
        );

        (count($logins) < 2) ?
            session()->invalidate() :
            session()->migrate();

        session()->regenerateToken();

        return app(LogoutResponse::class);
    }
}
