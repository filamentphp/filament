<?php

namespace Filament\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;

class RedirectToHomeController
{
    public function __invoke(): RedirectResponse
    {
        $panel = Filament::getCurrentPanel();

        abort_unless(blank($url = $panel->getUrl(Filament::getTenant())), 404);

        return redirect($url);
    }
}
