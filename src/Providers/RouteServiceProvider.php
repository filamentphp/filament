<?php

namespace Filament\Providers;

use Filament\Http\Middleware\Authenticate;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();

        Route::aliasMiddleware('auth.filament', Authenticate::class);
    }

    public function map()
    {
        Route::name('filament.')
            ->middleware(config('filament.middleware', ['web']))
            ->prefix(config('filament.prefix'))
            ->group(__DIR__ . '/../../routes/web.php');
    }
}
