<?php

namespace Alpine;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Arr;
use Illuminate\Events\Dispatcher;
use Alpine\Contracts\User as UserContract;
use Alpine\Traits\EventMap;
use Alpine\Http\Middleware\Authenticate;
use Alpine\Console\Commands\CreateUserCommand;
use Alpine\Http\Components\Alert as AlertComponent;

class AlpineServiceProvider extends ServiceProvider
{
    use EventMap;
    
    /**
     * Directory path to this package
     *
     * @var string
     */
    protected $packagePath = __DIR__.'/../';

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->handleSingeltons();
        $this->handleConfig();
        $this->handleBindings();
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleCommands();
        $this->handleEvents();
        $this->handleMiddleware();
        $this->handleRoutes();
        $this->handleMigrations();
        $this->handlePublishing();
        $this->handleBlade();
        $this->handleResources();
        $this->handleTranslations();
    }

    /**
     * Register the package's singletons.
     * 
     * @return void
     */
    protected function handleSingeltons()
    {
        $this->app->singleton('alpine', Alpine::class);
    }

    /**
     * Merge the packages's configs.
     * 
     * @return void
     */
    protected function handleConfig()
    {
        $this->mergeConfigFrom($this->packagePath.'config/alpine.php', 'alpine');
        if ($this->app['alpine']->handling()) {
            $this->mergeFromConfig('laravel-form-builder', $this->app['config']->get('alpine.laravel-form-builder', []));
        }
    }

    /**
     * Register the package's bindings.
     * 
     * @return void
     */
    protected function handleBindings()
    {
        $this->app->bind(UserContract::class, config('auth.providers.users.model'));
        // $this->app->bind(ResourceContract::class, config('alpine.models.resource'));
    }

    /**
     * Register the package's commands.
     * 
     * @return void
     */
    protected function handleCommands()
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
    protected function handleEvents()
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
    protected function handleMiddleware()
    {
        Route::aliasMiddleware('auth.alpine', Authenticate::class);
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function handleRoutes()
    {
        $namespace = 'Alpine\Http\Controllers';
        $name = 'alpine.';

        Route::middleware(config('alpine.middleware.web'))
            ->prefix(config('alpine.path')) 
            ->namespace($namespace) 
            ->name($name) 
            ->group(__DIR__.'/routes/web.php');

        Route::middleware(config('alpine.middleware.api'))
            ->prefix(config('alpine.path').'/api') 
            ->namespace($namespace) 
            ->name($name) 
            ->group(__DIR__.'/routes/api.php');
    }

    /**
     * Register the resources.
     *
     * @return void
     */
    protected function handleResources()
    {
        $this->loadViewsFrom($this->packagePath.'resources/views', 'alpine');
    }

    /**
     * Register the translations.
     *
     * @return void
     */
    protected function handleTranslations()
    {
        $this->loadTranslationsFrom($this->packagePath.'resources/lang', 'alpine');
    }

    /**
     * Register the package's migrations.
     *
     * @return void
     */
    protected function handleMigrations()
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
    protected function handlePublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->packagePath.'config/alpine.php' => config_path('alpine.php'),
            ], 'alpine-config');
        }
    }

    /**
     * Register the package's custom blade components and directives.
     * 
     * @return void
     */
    public function handleBlade()
    {
        Blade::directive('alpineAssets', [AlpineBladeDirectives::class, 'alpineAssets']);
        Blade::component(AlertComponent::class, 'alpine-alert');
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }

    /**
     * Merge the existing configuration with the given configuration.
     *
     * @param  string  $key
     * @param  string  $path
     * @return void
     */
    protected function mergeFromConfig($key, $config)
    {
        $this->app['config']->set($key, $this->mergeConfig($this->app['config']->get($key, []), $config));
    }

    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     *
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeConfig(array $original, array $merging)
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }
    
}
