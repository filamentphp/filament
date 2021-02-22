<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthorizeResourcePageRoute
{
    public function handle(Request $request, Closure $next, $resource, $page)
    {
        abort_unless($resource::authorizationManager()->can($page), 403);

        return $next($request);
    }
}
