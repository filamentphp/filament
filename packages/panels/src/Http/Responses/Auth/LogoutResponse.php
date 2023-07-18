<?php

namespace Filament\Http\Responses\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->to(
            Filament::hasLogin() ? Filament::getLoginUrl() : Filament::getUrl(),
        );
    }
}
