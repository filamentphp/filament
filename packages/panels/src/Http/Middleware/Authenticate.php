<?php

namespace Filament\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Inertia\Inertia;

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

            return; /** @phpstan-ignore-line */
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $panel = Filament::getCurrentOrDefaultPanel();

        // Security: If the user model does not implement `FilamentUser`,
        // access is only allowed in local environments. In production,
        // implement `FilamentUser` with `canAccessPanel()`.
        abort_if(
            $user instanceof FilamentUser ?
                (! $user->canAccessPanel($panel)) :
                (config('app.env') !== 'local'),
            403,
        );
    }

    /**
     * @param  array<string>  $guards
     * @return never
     */
    protected function unauthenticated($request, array $guards)
    {
        if (
            $request->header('X-Inertia') &&
            (! $request->expectsJson()) &&
            Filament::getCurrentOrDefaultPanel()->hasPlugin('inertia') &&
            filled($loginUrl = $this->redirectTo($request))
        ) {
            throw new HttpResponseException(Inertia::location(redirect()->guest($loginUrl)));
        }

        parent::unauthenticated($request, $guards);
    }

    protected function redirectTo($request): ?string
    {
        return Filament::getLoginUrl();
    }
}
