<?php

namespace Filament\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Filament\Models\Contracts\FilamentUser;

class UserTimeZone
{
    public function handle(Request $request, Closure $next)
    {
        $this->setTimeZone($request);
        return $next($request);
    }

    protected function setTimeZone(Request $request): void
    {
        if ($request->user() && $request->user()->getAttribute('timezone') !== null) {
            Config::set('request.user.timezone', $request->user()->getAttribute('timezone'));
            return;
        }

        Config::set(
            'request.user.timezone',
            $request->header(
                'x-timezone',
                strval(Config::get('filament.user_timezone', Config::get('app.timezone')))
            )
        );
    }

}
