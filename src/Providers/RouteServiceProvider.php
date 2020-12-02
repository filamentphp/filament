<?php

namespace Filament\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Filament\Filament;

class RouteServiceProvider extends ServiceProvider
{
    protected $name = 'filament.';

    public function boot(): void
    {
        parent::boot();
    }

    public function map(): void
    {
        $this->mapWebRoutes();
    }

    protected function prefix()
    {
        return config('filament.prefix.route');
    }

    protected function mapWebRoutes(): void
    {
        if (Filament::$registersRoutes) {
            Route::prefix($this->prefix())
                ->middleware(config('filament.middleware', ['web']))
                ->name($this->name)
                ->group(__DIR__.'/../../routes/web.php');
        }
    }
}