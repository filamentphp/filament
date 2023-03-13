<?php

namespace Filament\Http\Middleware;

use Filament\Context;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $context = Filament::getCurrentContext();

        abort_if(
            $user instanceof FilamentUser ?
                (! $user->canAccessFilament($context)) :
                (config('app.env') !== 'local'),
            403,
        );

        if (! $context->hasTenancy()) {
            return;
        }

        $tenant = Filament::getTenant();
        if ($tenant && $user instanceof HasTenants && $user->canAccessTenant($tenant)) {
            Filament::setTenant($tenant);

            return;
        }

        abort(404);
    }

    protected function redirectTo($request): string
    {
        return Filament::getLoginUrl();
    }
}
