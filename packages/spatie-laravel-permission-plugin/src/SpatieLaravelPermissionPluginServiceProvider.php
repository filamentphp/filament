<?php

namespace Filament;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Filament\Commands\MakePermissionCommand;
use Filament\Resources\RoleResource;

class SpatieLaravelPermissionPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('command.make:filament-resource', function(){
            return new MakePermissionCommand;
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/filament-spatie-laravel-permission-plugin.php', 'filament-spatie-laravel-permission-plugin');
        Facades\Filament::registerResources($this->getResources());
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->getCommands());
            $this->publishes([
                __DIR__ . '/../config/filament-spatie-laravel-permission-plugin.php' => config_path('filament-spatie-laravel-permission-plugin.php'),
            ], 'filament-spatie-laravel-permission-plugin-config');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/filament-spatie-laravel-permission-plugin-translations'),
            ], 'filament-spatie-laravel-permission-plugin-translations');
        }

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-spatie-laravel-permission-plugin');
    }

    protected function mergeConfig(array $original, array $merging): array
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

            if ($key === 'middleware' || $key === 'register') {
                continue;
            }

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }

    protected function mergeConfigFrom($path, $key): void
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }

    protected function getCommands(): array
    {
        $commands = [
            Commands\MakePermissionCommand::class,
        ];

        $aliases = [];

        foreach ($commands as $command) {
            $class = 'Filament\\Commands\\Aliases\\' . class_basename($command);

            if (! class_exists($class)) {
                continue;
            }

            $aliases[] = $class;
        }

        return array_merge($commands, $aliases);
    }

    public function getResources()
    {
        return [
            \Filament\Resources\RoleResource::class,
            \Filament\Resources\PermissionResource::class,
        ];
    }
}
