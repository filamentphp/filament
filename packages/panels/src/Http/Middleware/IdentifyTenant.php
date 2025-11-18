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

        $tenantRouteParameter = $request->route()->parameter('tenant');
        $tenantSlugAttribute = $panel->getTenantSlugAttribute();
        $currentTenant = Filament::getTenant();

        $currentTenantIdentifier = filled($tenantSlugAttribute)
            ? $currentTenant?->getAttribute($tenantSlugAttribute)
            : $currentTenant?->getRouteKey();

        if ($currentTenant && $currentTenantIdentifier === $tenantRouteParameter) {
            $tenant = $currentTenant;
        } else {
            $tenant = $panel->getTenant($tenantRouteParameter);
            Filament::setTenant($tenant);
        }

        if (! $user->canAccessTenant($tenant)) {
            abort(404);
        }

        return $next($request);
    }
}
