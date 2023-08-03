<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\HasTenants;
use Filament\Exceptions\NonTenantModelException;
use Filament\Exceptions\TenantAuthorizationException;

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
            throw new NonTenantModelException();
        }

        $tenant = $panel->getTenant($request->route()->parameter('tenant'));

        if (! $user->canAccessTenant($tenant)) {
            throw new TenantAuthorizationException();
        }

        Filament::setTenant($tenant);

        return $next($request);
    }
}
