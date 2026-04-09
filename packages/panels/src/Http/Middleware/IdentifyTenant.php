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
        // Security: Tenant identification occurs in this middleware. Global
        // scopes for tenant isolation are only active AFTER this runs.
        // Queries in earlier middleware or service providers will not
        // be tenant-scoped. Ensure tenant-aware middleware uses
        // `isPersistent: true` for Livewire AJAX enforcement.

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

        $tenant = $panel->getTenant($request->route()->parameter('tenant'));

        if (! $user->canAccessTenant($tenant)) {
            abort(404);
        }

        Filament::setTenant($tenant);

        return $next($request);
    }
}
