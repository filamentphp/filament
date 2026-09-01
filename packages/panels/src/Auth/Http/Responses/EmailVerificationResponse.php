<?php

namespace Filament\Auth\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\EmailVerificationResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class EmailVerificationResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect()->intended(Filament::getUrl());
    }
}
