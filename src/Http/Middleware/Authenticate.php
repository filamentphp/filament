<?php

namespace Filament\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{

    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated(Request $request, array $guards)
    {
        throw new AuthenticationException(
            __('filament::auth.unauthenticated'), $guards, $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {        
        if (! $request->expectsJson()) {
            session()->flash('notification', [
                'type' => 'warning',
                'message' => __('filament::auth.sign_in'),
            ]);

            return route('filament.auth.login');
        }
    }
}
