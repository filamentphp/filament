<?php

namespace Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests
{
    /** @param class-string<Middleware> $middleware */
    public function handle(Request $request, Closure $next, string $middleware): Response
    {
        $request->attributes->set(static::class, $middleware);

        return app($middleware)->handle($request, $next);
    }

    public function terminate(Request $request, Response $response): void
    {
        $middleware = $request->attributes->get(static::class);

        if ($middleware && method_exists($middleware, 'terminate')) {
            app($middleware)->terminate($request, $response);
        }
    }
}
