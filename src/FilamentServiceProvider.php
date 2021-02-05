<?php

namespace Filament;

use Filament\Commands\MakeUserCommand;
use Filament\Http\Middleware\AuthorizeResourceRoute;
use Filament\Providers\RouteServiceProvider;
use Filament\Providers\ServiceProvider;
use Filament\Traits\CanRegisterLivewireComponentDirectories;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;

class FilamentServiceProvider extends ServiceProvider
{
    use CanRegisterLivewireComponentDirectories;

    public $singletons = [
        FilamentManager::class => FilamentManager::class,
        Navigation::class => Navigation::class,
    ];

    public function boot()
    {
        $this->bootAuthConfiguration();
        $this->bootCommands();
        $this->bootDirectives();
        $this->bootLoaders();
        $this->bootLivewireComponents();
        $this->bootMiddleware();
        $this->bootPublishing();
    }

    protected function bootAuthConfiguration()
    {
        $this->app['config']->set('auth.guards.filament', [
            'driver' => 'session',
            'provider' => 'filament_users',
        ]);

        $this->app['config']->set('auth.passwords.filament_users', [
            'provider' => 'filament_users',
            'table' => 'filament_password_resets',
            'expire' => 60,
            'throttle' => 60,
        ]);

        $this->app['config']->set('auth.providers.filament_users', [
            'driver' => 'eloquent',
            'model' => \Filament\Models\FilamentUser::class,
        ]);
    }

    protected function bootCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            MakeUserCommand::class,
        ]);
    }

    protected function bootDirectives()
    {
        Blade::directive('filamentStyles', [BladeDirectives::class, 'styles']);
        Blade::directive('filamentScripts', [BladeDirectives::class, 'scripts']);
        Blade::directive('pushonce', [BladeDirectives::class, 'pushOnce']);
        Blade::directive('endpushonce', [BladeDirectives::class, 'endPushOnce']);
    }

    protected function bootLoaders()
    {
        $this->loadViewComponentsAs('filament', [
            'avatar' => \Filament\View\Components\Avatar::class,
            'image' => \Filament\View\Components\Image::class,
            'nav' => \Filament\View\Components\Nav::class,
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament');
    }

    protected function bootLivewireComponents()
    {
        $this->registerLivewireComponentDirectory(__DIR__ . '/Http/Livewire', 'Filament\\Http\\Livewire', 'filament.');
        $this->registerLivewireComponentDirectory(app_path('Filament/Resources'), 'App\\Filament\\Resources', 'filament.resources.');
    }

    protected function bootMiddleware()
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('filament.authorize.resource-route', AuthorizeResourceRoute::class);
    }

    protected function bootPublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../dist' => public_path('vendor/filament'),
        ], 'filament-assets');

        $this->publishes([
            __DIR__ . '/../config/filament.php' => config_path('filament.php'),
        ], 'filament-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/filament'),
        ], 'filament-lang');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament'),
        ], 'filament-views');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament.php', 'filament');

        $this->registerProviders();
    }

    protected function registerProviders()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
