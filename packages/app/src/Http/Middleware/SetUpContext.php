<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Context;
use Filament\Facades\Filament;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SetUpContext
{
    public function handle(Request $request, Closure $next, string $context): mixed
    {
        $context = Filament::getContext($context);

        Filament::setCurrentContext($context);

        Filament::bootCurrentContext();

        $this->setTenant($request, $context);

        return $next($request);
    }

    protected function setTenant(Request $request, Context $context): void
    {
        if (! $context->hasTenancy()) {
            return;
        }

        /** @var Model|null $user */
        $user = $context->auth()->user();

        if (! $context->hasRoutableTenancy()) {
            Filament::setTenant($user);

            return;
        }

        if (! $request->route()->hasParameter($context->getTenantRouteParameter())) {
            return;
        }

        $tenant = $context->getTenant($request->route()->parameter($context->getTenantRouteParameter()));
        Filament::setTenant($tenant);
    }
}
