<?php

namespace Filament;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class SpatieLaravelSettingsPluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeSettingsPageCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/filament-spatie-laravel-settings-plugin'),
            ], 'filament-spatie-laravel-settings-plugin-views');

            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-spatie-laravel-settings-plugin');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-spatie-laravel-settings-plugin');
    }
}
