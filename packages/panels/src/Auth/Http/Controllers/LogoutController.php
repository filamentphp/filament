<?php

namespace Filament\Auth\Http\Controllers;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Filament\Facades\Filament;

class LogoutController
{
    public function __invoke(): LogoutResponse
    {
        Filament::auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        return app(LogoutResponse::class);
    }
}
