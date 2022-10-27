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
    public function handle(Request $request, Closure $next, string $context)
    {
        $context = Filament::getContext($context);

        Filament::setCurrentContext($context);

        if ($context->hasTenancy()) {
            $this->setTenant($request, $context);
        }

        Filament::bootCurrentContext();

        return $next($request);
    }

    protected function setTenant(Request $request, Context $context): void
    {
        /** @var Model $user */
        $user = $context->auth()->user();

        if (! $context->hasRoutableTenancy()) {
            Filament::setTenant($user);

            return;
        }

        if (! $request->route()->hasParameter('tenant')) {
            return;
        }

        $tenant = $context->getTenant($request->route()->parameter('tenant'));

        if ($user instanceof HasTenants && $user->canAccessTenant($tenant)) {
            Filament::setTenant($tenant);

            return;
        }

        abort(404);
    }
}
