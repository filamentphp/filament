<?php

namespace Filament\Actions\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
class AuthenticateDownload implements AuthenticatesRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(auth(
            $request->hasValidSignature(absolute: false)
                ? $request->query('authGuard')
                : null,
        )->check(), 401);

        return $next($request);
    }
}
