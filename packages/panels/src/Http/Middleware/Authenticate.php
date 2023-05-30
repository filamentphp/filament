<?php

namespace Filament\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
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

        $panel = Filament::getCurrentPanel();

        abort_if(
            $user instanceof FilamentUser ?
                (! $user->canAccessFilament($panel)) :
                (config('app.env') !== 'local'),
            403,
        );
    }

    protected function setTenant(Request $request, Panel $panel): void
    {
        if (! $request->route()->hasParameter('tenant')) {
            return;
        }

        $tenant = $panel->getTenant($request->route()->parameter('tenant'));

        /** @var Model $user */
        $user = $panel->auth()->user();

        if ($user instanceof HasTenants && $user->canAccessTenant($tenant)) {
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
