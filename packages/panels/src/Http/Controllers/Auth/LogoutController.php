<?php

namespace Filament\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;

class LogoutController
{
    public function __invoke(): LogoutResponse
    {
        Filament::auth()->logout();

        $count = collect(session()->all())->filter(function ($value, $key) {
            return strpos($key, 'login_') === 0;
        })->count();

        ($count == 1)
            ? session()->invalidate()
            : session()->migrate();        
        
        session()->regenerateToken();

        return app(LogoutResponse::class);
    }
}
