<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableBladeIconComponents
{
    public function handle(Request $request, Closure $next): mixed
    {
        config()->set('blade-icons.components.disabled', true);

        return $next($request);
    }
}
