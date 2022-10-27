<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;

class SetUpContext
{
    public function handle(Request $request, Closure $next, string $context)
    {
        $context = Filament::getContext($context);

        Filament::setCurrentContext($context);

        if ($context->hasTenancy() && $request->filled('tenant')) {
            Filament::setTenant($context->getTenant($request->get('tenant')));
        }

        Filament::bootCurrentContext();

        return $next($request);
    }
}
