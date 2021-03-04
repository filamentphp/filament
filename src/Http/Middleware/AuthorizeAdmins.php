<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Filament;
use Illuminate\Http\Request;

class AuthorizeAdmins
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(Filament::auth()->user()->isFilamentAdmin(), 403);

        return $next($request);
    }
}
