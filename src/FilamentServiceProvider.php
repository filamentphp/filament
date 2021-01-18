<?php

namespace Filament;

use Filament\Commands\MakeUser;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use Filament\Providers\{RouteServiceProvider, ServiceProvider};

class FilamentServiceProvider extends ServiceProvider
{
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
        $this->bootNavigation();
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
            'model' => \Filament\Models\User::class,
        ]);
    }

    protected function bootCommands()
    {
        if (! $this->app->runningInConsole()) return;

        $this->commands([
            MakeUser::class,
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
        $this->loadViewComponentsAs('filament', config('filament.components', []));

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament');
    }

    protected function bootNavigation()
    {
        $this->app->booted(function () {
            if (Features::hasDashboard()) {
                $this->app[Navigation::class]->dashboard = config('filament.nav.dashboard', [
                    'path' => 'filament.dashboard',
                    'active' => 'filament.dashboard',
                    'label' => 'Dashboard',
                    'icon' => 'heroicon-o-home',
                    'sort' => -9999,
                ]);
            }

            if (Features::hasResources()) {
                $this->app[FilamentManager::class]->resources()->each(function ($item, $key) {
                    $resource = $this->app->make($item);

                    if ($resource->enabled && array_key_exists('index', $resource->actions())) {
                        $route = route('filament.resource', ['resource' => $key]);
                        $routePath = implode('/', array_slice(explode('/', $route), -3, 2, true)).'/'.$key;

                        $this->app[Navigation::class]->$key = [
                            'path' => $route,
                            'active' => [
                                $routePath,
                                $routePath.'/*',
                            ],
                            'label' => $resource->label(),
                            'icon' => $resource->icon,
                            'sort' => $resource->sort,
                        ];
                    }
                });
            }
        });
    }

    protected function bootPublishing()
    {
        if (! $this->app->runningInConsole()) return;

        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/filament'),
        ], 'filament-assets');

        $this->publishes([
            __DIR__.'/../config/filament.php.stub' => config_path('filament.php'),
        ], 'filament-config');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/filament'),
        ], 'filament-lang');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/filament'),
        ], 'filament-views');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament.php', 'filament');

        $this->registerLivewireComponents();

        $this->registerProviders();
    }

    protected function registerLivewireComponents()
    {
        $this->app->afterResolving(BladeCompiler::class, function () {
            foreach (config('filament.livewire', []) as $alias => $component) {
                Livewire::component("filament-$alias", $component);
            }
        });
    }

    protected function registerProviders()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
