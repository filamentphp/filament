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
        $panel = Filament::getCurrentOrDefaultPanel();

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

        $currentTenant = Filament::getTenant();
        $slugAttribute = $panel->getTenantSlugAttribute();
        $tenantRouteKey = $request->route()->parameter('tenant');

        $currentTenantKey = filled($slugAttribute)
            ? $currentTenant?->getAttribute($slugAttribute)
            : $currentTenant?->getRouteKey();

        if ($currentTenant && $currentTenantKey === $tenantRouteKey) {
            $tenant = $currentTenant;
        } else {
            $tenant = $panel->getTenant($tenantRouteKey);
            Filament::setTenant($tenant);
        }

        if (! $user->canAccessTenant($tenant)) {
            abort(404);
        }

        return $next($request);
    }
}
