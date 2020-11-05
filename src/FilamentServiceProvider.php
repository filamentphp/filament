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
use Illuminate\Support\Str;
use Livewire\Livewire;
use Filament\BladeDirectives;
use Filament\Commands\{
    MakeUser,
};
use Filament\Providers\RouteServiceProvider;
use Filament\Features;
use Filament\Models\Navigation;

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
        $this->bootResources();
        $this->bootDirectives();
        $this->bootResourceModels();
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

    protected function registerProviders(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function bootModelBindings(): void
    {
        $models = $this->app->config['filament.models'];

        if (!$models) {
            return;
        }

        $this->app->bind('Filament\Navigation', $models['navigation']);
        $this->app->bind('Filament\User', $models['user']);
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

    protected function bootResourceModels(): void
    {   
        if (Features::hasResourceModels()) {
            $this->app->booted(function () {
                $models = $this->app->filament->getResourceModels();
                
                $models->each(function ($item, $key) {
                    $model = $this->app->make($item);

                    if (array_key_exists('index', $model->actions())) {
                        $route = route('filament.resource', ['model' => $key]);

                        Navigation::create([
                            'path' => $route,
                            'active' => $route.'*',
                            'label' => $model->label ?? Str::plural($key),
                            'icon' => $model->icon ?? 'heroicon-o-database',
                            'sort' => $model->sort ?? 0,
                        ]);
                    }
                });
            });
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
