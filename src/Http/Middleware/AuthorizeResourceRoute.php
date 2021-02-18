<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Models\Resource;
use Illuminate\Http\Request;

class AuthorizeResourceRoute
{
    public function handle(Request $request, Closure $next, $resource, $route)
    {
        abort_unless($resource::authorizationManager()->can($route), 403);

        return $next($request);
    }
}
