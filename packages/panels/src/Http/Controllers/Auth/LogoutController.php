<?php

namespace Filament\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;

class LogoutController
{
    public function __invoke(): LogoutResponse
    {
        Filament::auth()->logout();

        return app(LogoutResponse::class);
    }
}
