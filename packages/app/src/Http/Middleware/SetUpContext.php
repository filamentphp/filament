<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Events\ServingFilament;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
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

        return $next($request);
    }
}
