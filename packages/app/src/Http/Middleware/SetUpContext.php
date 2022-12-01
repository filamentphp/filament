<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class SetUpContext
{
    public function handle(Request $request, Closure $next, string $context): mixed
    {
        $context = Filament::getContext($context);

        Filament::setCurrentContext($context);

        Filament::bootCurrentContext();

        return $next($request);
    }
}
