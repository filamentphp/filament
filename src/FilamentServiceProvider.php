<?php

namespace Filament;

use Illuminate\Support\Facades\{
    File,
    Blade,
};
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Livewire\{
    Livewire,
    Component,
};
use Filament\Providers\{
    ServiceProvider,
    AuthServiceProvider,
    RouteServiceProvider,
};
use Filament\BladeDirectives;
use Filament\Contracts\User as UserContract;
use Filament\Commands\{
    MakeUser,
    MakeFieldset,
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
        $this->registerSingeltons();
        $this->registerConfig();
        $this->registerBindings();
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootProviders();
        $this->bootCommands();
        $this->bootMigrations();
        $this->bootPublishing();
        $this->bootBladeDirectives();
        $this->bootBladeComponentAutoDiscovery();
        $this->bootLivewireComponentAutoDiscovery();
        $this->bootResources();
        $this->bootTranslations();
    }

    /**
     * Register the package's singletons.
     * 
     * @return void
     */
    protected function registerSingeltons()
    {
        $this->app->singleton('filament', Filament::class);
    }

    /**
     * Merge the packages's configs.
     * 
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom($this->app['filament']->basePath('config/filament.php'), 'filament');
        /*
        if ($this->app['filament']->handling()) {
            $this->mergeFromConfig('existing-package-config', $this->app['config']->get('filament.existing-package-config', []));
        }
        */
    }

    /**
     * Register the package's bindings.
     * 
     * @return void
     */
    protected function registerBindings()
    {
        $this->app->bind(UserContract::class, config('auth.providers.users.model'));
    }

    /**
     * Register the package's providers.
     * 
     * @return void
     */
    protected function bootProviders()
    {
        $this->app->register(AuthServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register the package's commands.
     * 
     * @return void
     */
    protected function bootCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeUser::class,
                MakeFieldset::class,
            ]);
        }
    }

    /**
     * Register the resources.
     *
     * @return void
     */
    protected function bootResources()
    {
        $this->loadViewsFrom($this->app['filament']->basePath('resources/views'), 'filament');
    }

    /**
     * Register the translations.
     *
     * @return void
     */
    protected function bootTranslations()
    {
        $this->loadTranslationsFrom($this->app['filament']->basePath('resources/lang'), 'filament');
    }

    /**
     * Register the package's migrations.
     *
     * @return void
     */
    protected function bootMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom($this->app['filament']->basePath('database/migrations'));
            $this->loadMigrationsFrom(base_path('vendor/appstract/laravel-meta/database/migrations'));
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function bootPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->app['filament']->basePath('config/filament.php') => config_path('filament.php'),
            ], 'filament-config');
            
            $this->publishes([
                $this->app['filament']->basePath('database/seeds/FilamentSeeder.php') => database_path('seeds/FilamentSeeder.php'),
                $this->app['filament']->basePath('database/seeds/FilamentPermissionSeeder.php') => database_path('seeds/FilamentPermissionSeeder.php'),
            ], 'filament-seeds');
        }
    }

    /**
     * Blade directives
     */
    public function bootBladeDirectives()
    {
        Blade::directive('filamentAssets', [BladeDirectives::class, 'assets']);
    }

    /**
     * Blade component auto-discovery
     * 
     * @return void
     */
    public function bootBladeComponentAutoDiscovery()
    {
        $components = collect((new Filesystem)->allFiles(__DIR__.'/Http/Components'))->map(function (SplFileInfo $file) {
            $baseName = class_basename(str_replace('.php', '', $file->getPathname()));
            return "\\Filament\\Http\\Components\\{$baseName}";
        })->all();

        $this->loadViewComponentsAs('filament', $components);
    }

    /**
     * Livewire component auto-discovery
     * 
     * @return void
     */
    public function bootLivewireComponentAutoDiscovery()
    {
        // Ensure Livewire directory exists in the app 
        if (!File::exists(app_path('Http/Livewire'))) {
            File::makeDirectory(app_path('Http/Livewire'));
        }

        // Automatically register Livewire components
        collect((new Filesystem)->allFiles(__DIR__.'/Http/Livewire'))->map(function (SplFileInfo $file) {
            $baseName = class_basename(str_replace('.php', '', $file->getPathname()));
            Livewire::component('filament::'.Str::of($baseName)->kebab(), "\\Filament\\Http\\Livewire\\{$baseName}");
        });     
    }
}
