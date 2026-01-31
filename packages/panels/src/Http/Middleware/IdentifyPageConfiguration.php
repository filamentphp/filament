<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class IdentifyPageConfiguration
{
    public function handle(Request $request, Closure $next, string $key): mixed
    {
        Filament::setCurrentPageConfigurationKey($key);

        return $next($request);
    }
}
