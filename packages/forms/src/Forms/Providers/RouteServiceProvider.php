<?php

namespace Filament\Forms\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function map()
    {
        Route::name('filament.')
            ->middleware(['web'])
            ->prefix(config('filament.api_path'))
            ->group(__DIR__ . '/../../../routes/web.php');
    }
}
