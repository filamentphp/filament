<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Support\Facades\{
    Route,
    Gate,
    Blade,
};
use Livewire\Livewire;
use Filament\BladeDirectives;
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
    }

    public function boot(): void
    {
        $this->bootModelBindings();
        $this->bootPolicies();
        $this->bootResources();
        $this->bootDirectives();
        $this->bootRoutes();
        $this->bootCommands();
        $this->bootPublishing();
    }

    protected function registerSingletons(): void
    {
        $this->app->singleton('filament', Filament::class);
    }

    protected function registerLivewireComponents(): void
    {
        $this->app->afterResolving(BladeCompiler::class, function () {
            $prefix = config('filament.prefix.component', '');

            /** @var LivewireComponent $component */
            foreach (config('filament.livewire', []) as $alias => $component) {
                $alias = $prefix ? "$prefix-$alias" : $alias;

                Livewire::component($alias, $component);
            }
        });
    }

    protected function bootModelBindings(): void
    {
        $models = $this->app->config['filament.models'];

        if (! $models) {
            return;
        }

        $this->app->bind('Filament\Navigation', $models['navigation']);
        $this->app->bind('Filament\User', $models['user']);
    }

    protected function bootPolicies(): void
    {
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'Filament\\Policies\\'.class_basename($modelClass).'Policy';
        });
    }

    protected function bootResources(): void
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
    }

    protected function bootPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
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

    protected function bootRoutes(): void
    {
        if (Filament::$registersRoutes) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }
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
