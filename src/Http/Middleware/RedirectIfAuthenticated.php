<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::guard('filament')->check()) {
            return redirect()->route('filament.dashboard');
        }

        return $next($request);
    }
}
