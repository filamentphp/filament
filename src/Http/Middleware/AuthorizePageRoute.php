<?php

namespace Filament\Http\Middleware;

use Closure;
use Filament\Models\Page;
use Illuminate\Http\Request;

class AuthorizePageRoute
{
    public function handle(Request $request, Closure $next, $page)
    {
        abort_unless($page::authorizationManager()->can(), 403);

        return $next($request);
    }
}
