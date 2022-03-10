<?php

declare(strict_types=1);

namespace Filament\Http\Responses;

use Filament\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->to(
            config('filament.auth.pages.login') ? route('filament.auth.login') : config('filament.path')
        );
    }
}
