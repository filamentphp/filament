<?php

namespace Filament\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Livewire\Macros\RouteMacros;
use Livewire\Macros\RouterMacros;
use Filament\Http\Middleware\Authenticate;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to controller routes.
     *
     * https://laravel.com/docs/7.x/routing#route-group-namespaces
     * 
     * @var string
     */
    protected $namespace = 'Filament\Http\Controllers';

    /**
     * This route name prefix  is applied to controller routes.
     * 
     * @link https://laravel.com/docs/7.x/routing#route-group-name-prefixes
     * 
     * @var string
     */
    protected $name = 'filament.';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootMiddleware();
        $this->bootRouteMacros();

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {


        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Register package middleware.
     * 
     * @return void
     */
    protected function bootMiddleware()
    {
        Route::aliasMiddleware('auth.filament', Authenticate::class);
    }

    /**
     * Register the package custom route macros.
     *
     * @return void
     */
    protected function bootRouteMacros()
    {
        Route::mixin(new RouteMacros);
        Router::mixin(new RouterMacros);
    }

    /**
     * Return route prefix with optional appended path.
     * 
     * @var string
     * @return string
     */
    protected function prefix($path = null)
    {
        return config('filament.path').'/'.trim($path, '/');
    }

    /**
     * Define the "web" routes.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::prefix($this->prefix())
             ->middleware(config('filament.middleware.web'))
             ->namespace($this->namespace)
             ->name($this->name)
             ->group($this->app['filament']->basePath('routes/web.php'));
    }

    /**
     * Define the "api" routes.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix($this->prefix('api'))
             ->middleware(config('filament.middleware.api'))
             ->namespace($this->namespace)
             ->name($this->name) 
             ->group($this->app['filament']->basePath('routes/api.php'));
    }
}
