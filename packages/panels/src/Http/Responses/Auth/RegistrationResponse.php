<?php

namespace Filament\Http\Responses\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class RegistrationResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        return redirect()->intended(Filament::getUrl());
    }
}
