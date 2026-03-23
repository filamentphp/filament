<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->query('no_auto_login')) {
            return $next($request);
        }

        if (! auth()->check()) {
            $user = User::first();

            if ($user) {
                auth()->login($user);
            }
        }

        return $next($request);
    }
}
