<?php

namespace Filament;

use Filament\Commands\MakeUserCommand;
use Filament\Http\Middleware\AuthorizeResourceRoute;
use Filament\Models\FilamentUser;
use Filament\Providers\RouteServiceProvider;
use Filament\Support\Providers\ServiceProvider;
use Filament\Support\RegistersLivewireComponentDirectories;
use Filament\View\Components;
use Illuminate\Routing\Router;

class FilamentServiceProvider extends ServiceProvider
{
    use RegistersLivewireComponentDirectories;

    public $singletons = [
        FilamentManager::class => FilamentManager::class,
        Navigation::class => Navigation::class,
    ];

    public function boot()
    {
        $this->bootAssets();
        $this->bootAuthConfiguration();
        $this->bootCommands();
        $this->bootLoaders();
        $this->bootLivewireComponents();
        $this->bootMiddleware();
        $this->bootPublishing();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament.php', 'filament');

        $this->registerProviders();
    }

    protected function bootAssets()
    {
        Support::registerScript('filament.js', __DIR__.'/../dist/js/filament.js');
        Support::registerScript('filament.js.map', __DIR__.'/../dist/js/filament.js.map');

        Support::registerStyles('filament.css', __DIR__.'/../dist/css/filament.css');
        Support::registerStyles('filament.css.map', __DIR__.'/../dist/css/filament.css.map');
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
            'model' => FilamentUser::class,
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

    protected function bootLoaders()
    {
        $this->loadViewComponentsAs('filament', [
            'avatar' => Components\Avatar::class,
            'image' => Components\Image::class,
            'layouts.app' => Components\Layouts\App::class,
            'layouts.auth' => Components\Layouts\Auth::class,
            'layouts.base' => Components\Layouts\Base::class,
            'nav' => Components\Nav::class,
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

    protected function registerProviders()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
