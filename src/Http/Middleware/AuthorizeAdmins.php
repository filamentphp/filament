<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizeAdmins
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(Auth::guard('filament')->user()->is_admin, 403);

        return $next($request);
    }
}
