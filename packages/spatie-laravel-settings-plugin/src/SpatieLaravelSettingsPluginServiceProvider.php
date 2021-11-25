<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;

class SpatieLaravelSettingsPluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-spatie-laravel-settings-plugin');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament-spatie-laravel-settings-plugin');
    }
}
