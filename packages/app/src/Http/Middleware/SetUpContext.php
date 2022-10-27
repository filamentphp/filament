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

        if ($user::class === $context->getTenantModel()) {
            Filament::setTenant($user);

            return;
        }

        if (! $request->filled('tenant')) {
            return;
        }

        $tenant = $context->getTenant($request->get('tenant'));

        if ($user instanceof HasTenants && $user->canAccessTenant($tenant)) {
            Filament::setTenant($tenant);
        }

        abort(404);
    }
}
