<?php

namespace Filament\Http\Controllers;

use Filament\Facades\Filament;
use Filament\Http\Responses\Contracts\LogoutResponse;

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
