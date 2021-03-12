<?php

namespace Filament\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

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
