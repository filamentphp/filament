<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): mixed
    {
        $context = Filament::getCurrentContext();

        if (! $context->hasTenancy()) {
            return $next($request);
        }

        if (! $request->route()->hasParameter('tenant')) {
            return $next($request);
        }

        /** @var Model $user */
        $user = $context->auth()->user();

        if (! $user instanceof HasTenants) {
            abort(404);
        }

        $tenant = $context->getTenant($request->route()->parameter('tenant'));

        if (! $user->canAccessTenant($tenant)) {
            abort(404);
        }

        Filament::setTenant($tenant);

        return $next($request);
    }
}
