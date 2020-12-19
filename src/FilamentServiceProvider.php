<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Support\Facades\{
    Route,
    Gate,
    Blade,
    File,
    Cache,
};
use Livewire\Livewire;
use Spatie\Valuestore\Valuestore;
use Filament\Providers\RouteServiceProvider;
use Filament\Features;
use Filament\Commands\{
    MakeUser,
};

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filament.php', 'filament');
        $this->registerSingletons();
        $this->registerLivewireComponents();      
        $this->registerProviders();
    }

    public function boot(): void
    {
        $this->bootModelBindings();
        $this->bootLoaders();
        $this->bootDirectives();
        $this->bootNavigation();
        $this->bootCommands();
        $this->bootPublishing();
    }

    protected function registerSingletons(): void
    {
        $this->app->singleton('filament', Filament::class);

        $this->app->singleton('Filament\Navigation', function () {
            return new Navigation();
        });
    }

    protected function registerLivewireComponents(): void
    {
        $this->app->afterResolving(BladeCompiler::class, function () {
            $prefix = config('filament.prefix.component', '');

            foreach (config('filament.livewire', []) as $alias => $component) {
                $alias = $prefix ? "$prefix-$alias" : $alias;

                Livewire::component($alias, $component);
            }
        });
    }

    protected function registerProviders(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function bootModelBindings(): void
    {
        $models = config('filament.models');

        if (!$models) {
            return;
        }

        $this->app->bind('Filament\User', $models['user']);
    }

    protected function bootLoaders(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament');
        $this->loadViewComponentsAs(config('filament.prefix.component', 'filament'), config('filament.components', []));
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filament');
    }

    protected function bootDirectives(): void
    {
        Blade::directive('filamentStyles', [BladeDirectives::class, 'styles']);
        Blade::directive('filamentScripts', [BladeDirectives::class, 'scripts']);
        Blade::directive('pushonce', [BladeDirectives::class, 'pushOnce']);
        Blade::directive('endpushonce', [BladeDirectives::class, 'endPushOnce']);
    }

    protected function bootPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/filament.php' => config_path('filament.php'),
        ], 'filament-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/filament'),
        ], 'filament-views');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/filament'),
        ], 'filament-lang');

        $this->publishes([
            __DIR__.'/../dist' => public_path('vendor/filament'),
        ], 'filament-assets');
    }

    protected function bootNavigation(): void
    {   
        $this->app->booted(function () {

            if (Features::hasDashboard()) {
                app('Filament\Navigation')->dashboard = config('filament.nav.dashboard', [
                    'path' => 'filament.dashboard',
                    'active' => 'filament.dashboard',
                    'label' => 'Dashboard',
                    'icon' => 'heroicon-o-home',
                    'sort' => -9999,
                ]);
            }

            if (Features::hasResources()) {         
                app('filament')->resources()->each(function ($item, $key) {
                    $resource = $this->app->make($item);

                    if ($resource->enabled && array_key_exists('index', $resource->actions())) {
                        $route = route('filament.resource', ['resource' => $key]);
                        $routePath = implode('/', array_slice(explode('/', $route), -3, 2, true)).'/'.$key;

                        app('Filament\Navigation')->$key = [
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

    protected function bootCommands(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }
            
        $this->commands([
            MakeUser::class,
        ]);
    }
}
