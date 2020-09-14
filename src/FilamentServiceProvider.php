<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{
    Route,
    Gate,
};
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Livewire\{
    Livewire,
    Component,
};

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filament.php', 'filament');
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament');
        $this->configurePolicies();
        $this->configureComponents();
        $this->configureLivewireComponents();
        $this->configurePublishing();
        $this->configureRoutes();
    }

    /**
     * Configure the package policies.
     */
    protected function configurePolicies()
    {
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'Filament\\Policies\\'.class_basename($modelClass).'Policy';
        });
    }

    /**
     * Configure the Filament Blade components.
     *
     * @return void
     */
    protected function configureComponents()
    {
        $components = collect((new Filesystem)->allFiles(__DIR__.'/View/Components'))->map(function (SplFileInfo $file) {
            $baseName = class_basename(str_replace('.php', '', $file->getPathname()));
            return "\\Filament\\View\\Components\\{$baseName}";
        })->all();

        $this->loadViewComponentsAs('filament', $components);
    }

    /**
     * Configure the Filament Livewire components.
     * 
     * @return void
     */
    protected function configureLivewireComponents()
    {
        collect((new Filesystem)->allFiles(__DIR__.'/Http/Livewire'))->map(function (SplFileInfo $file) {
            $baseName = class_basename(str_replace('.php', '', $file->getPathname()));
            Livewire::component('filament::'.Str::of($baseName)->kebab(), "\\Filament\\Http\\Livewire\\{$baseName}");
        });     
    }

    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    protected function configurePublishing()
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
            // __DIR__.'/../database/migrations/2020_09_13_200000_create_resources_table.php' => database_path('migrations/2020_09_13_200000_create_resources_table.php'),
        ], 'filament-resources-migrations');

        $this->publishes([
            $this->getRoutes() => base_path('routes/filament.php'),
        ], 'filament-routes');
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        if (Filament::$registersRoutes) {
            Route::group([
                'namespace' => 'Filament\Http\Controllers',
            ], function () {
                $this->loadRoutesFrom($this->getRoutes());
            });
        }
    }

    /**
     * Get the routes for the application.
     * 
     * @return string
     */
    protected function getRoutes()
    {
        return __DIR__.'/../routes/'.config('filament.stack').'.php';
    }
}