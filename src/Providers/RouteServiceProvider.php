<?php

namespace Filament\Providers;

use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\EncryptCookies;
use Filament\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class RouteServiceProvider extends ServiceProvider
{
    public function map()
    {
        Route::name('filament.')
            ->middleware(config('filament.middleware.base'))
            ->domain(config('filament.domain'))
            ->prefix(config('filament.path'))
            ->group(__DIR__ . '/../../routes/web.php');
    }
}
