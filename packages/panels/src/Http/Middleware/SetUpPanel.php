<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class SetUpPanel
{
    public function handle(Request $request, Closure $next, string $panel): mixed
    {
        $panel = Filament::getPanel($panel);

        Filament::setCurrentPanel($panel);
        Filament::setCurrentDomain(null);
        Filament::setCurrentPageConfigurationKey(null);
        Filament::setCurrentResourceConfigurationKey(null);
        Filament::setTenant(null, isQuiet: true);

        Filament::bootCurrentPanel();

        return $next($request);
    }
}
