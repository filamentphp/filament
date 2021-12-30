<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;

class SpatieLaravelPermissionPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('command.make:filament-resource', function () {
            return new Commands\ExtendsMakeResourceCommand();
        });
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

    protected function getCommands(): array
    {
        $commands = [
            Commands\ExtendsMakeResourceCommand::class,
            Commands\InstallCommand::class,
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
}
