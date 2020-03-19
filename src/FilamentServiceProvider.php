<?php

namespace Filament;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Illuminate\Events\Dispatcher;
use Livewire\Livewire;
use Filament\Support\ServiceProvider;
use Filament\Providers\AuthServiceProvider;
use Filament\Support\BladeDirectives;
use Filament\Contracts\User as UserContract;
use Filament\Traits\EventMap;
use Filament\Http\Middleware\Authenticate;
use Filament\Commands\CreateUserCommand;

class FilamentServiceProvider extends ServiceProvider
{
    use EventMap;

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
        $this->registerAuth();
        $this->registerCommands();
        $this->registerEvents();
        $this->registerMiddleware();
        $this->registerRoutes();
        $this->registerMigrations();
        $this->registerPublishing();
        $this->registerBlade();
        $this->registerLivewire();
        $this->registerResources();
        $this->registerTranslations();
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
        $this->mergeConfigFrom($this->packagePath.'config/filament.php', 'filament');
        if ($this->app['filament']->handling()) {
            // $this->mergeFromConfig('existing-package-config', $this->app['config']->get('filament.existing-package-config', []));
        }
    }

    /**
     * Register the package's bindings.
     * 
     * @return void
     */
    protected function registerBindings()
    {
        $this->app->bind(UserContract::class, config('auth.providers.users.model'));
        // $this->app->bind(ResourceContract::class, config('filament.models.resource'));
    }

    /**
     * Register the package's auth.
     * 
     * @return void
     */
    protected function registerAuth()
    {
        $this->app->register(AuthServiceProvider::class);
    }

    /**
     * Register the package's commands.
     * 
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateUserCommand::class,
            ]);
        }
    }

    /**
     * Register the events and listeners.
     *
     * @return void
     * @throws BindingResolutionException
     */
    protected function registerEvents()
    {
        $events = $this->app->make(Dispatcher::class);

        foreach ($this->events as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * Register package middleware.
     * 
     * @return void
     */
    protected function registerMiddleware()
    {
        Route::aliasMiddleware('auth.filament', Authenticate::class);
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        $namespace = 'Filament\Http\Controllers';
        $name = 'filament.';

        Route::middleware(config('filament.middleware.web'))
            ->prefix(config('filament.path')) 
            ->namespace($namespace) 
            ->name($name) 
            ->group($this->packagePath.'routes/web.php');

        Route::middleware(config('filament.middleware.api'))
            ->prefix(config('filament.path').'/api') 
            ->namespace($namespace) 
            ->name($name) 
            ->group($this->packagePath.'routes/api.php');
    }

    /**
     * Register the resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom($this->packagePath.'resources/views', 'filament');
    }

    /**
     * Register the translations.
     *
     * @return void
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom($this->packagePath.'resources/lang', 'filament');
    }

    /**
     * Register the package's migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom($this->packagePath.'database/migrations');
        }
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->packagePath.'config/filament.php' => config_path('filament.php'),
            ], 'filament-config');
        }
    }

    /**
     * Register the package's custom blade components and directives.
     * 
     * @return void
     */
    public function registerBlade()
    {
        // Register package directives
        Blade::directive('filamentAssets', [BladeDirectives::class, 'filamentAssets']);

        // Automatically register package components
        foreach (File::glob(__DIR__.'/Http/Components/*.php') as $path) {
            $baseName = basename($path, '.php');
            Blade::component("\\Filament\\Http\\Components\\{$baseName}", 'filament-'.Str::of($baseName)->kebab());
        }
    }

    /**
     * Livewire component registration / setup.
     * 
     * @return void
     */
    public function registerLivewire()
    {
        // Ensure Livewire directory exists in the app 
        if (!File::exists(app_path('Http/Livewire'))) {
            File::makeDirectory(app_path('Http/Livewire'));
        }

        // Automatically register package components
        foreach (File::glob(__DIR__.'/Http/Livewire/*.php') as $path) {
            $baseName = basename($path, '.php');
            Livewire::component('filament::'.Str::of($baseName)->kebab(), "\\Filament\\Http\\Livewire\\{$baseName}");
        }
    }
}
