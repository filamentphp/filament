<?php

namespace Filament\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;

class RedirectToHomeController
{
    public function __invoke(): RedirectResponse
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        $url = $panel->getRedirectUrl(Filament::getTenant());

        if (blank($url)) {
            abort(404);
        }

        return redirect($url);
    }
}
