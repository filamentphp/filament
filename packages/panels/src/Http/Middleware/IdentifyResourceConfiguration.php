<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class IdentifyResourceConfiguration
{
    public function handle(Request $request, Closure $next, string $key): mixed
    {
        Filament::setCurrentResourceConfigurationKey($key);

        return $next($request);
    }
}
