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
        $panel = Filament::getCurrentPanel();

        if (! $panel->hasTenancy()) {
            return $next($request);
        }

        if (! $request->route()->hasParameter('tenant')) {
            return $next($request);
        }

        /** @var Model $user */
        $user = $panel->auth()->user();

        if (! $user instanceof HasTenants) {
            abort(404);
        }

        $tenant = $panel->getTenant($request->route()->parameter('tenant'));

        if (! $user->canAccessTenant($tenant)) {
            abort(404);
        }

        Filament::setTenant($tenant);

        return $next($request);
    }
}
