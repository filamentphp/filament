<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;

class SpatieLaravelSettingsPluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MakeSettingsPageCommand::class,
            ]);
        }

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-spatie-laravel-settings-plugin');
    }
}
