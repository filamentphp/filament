<?php

namespace Filament;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class SpatieLaravelTranslatablePluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-spatie-laravel-translatable-plugin.php', 'filament-spatie-laravel-translatable-plugin');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/filament-spatie-laravel-translatable-plugin.php' => config_path('filament-spatie-laravel-translatable-plugin.php'),
            ], 'filament-spatie-laravel-translatable-plugin-config');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/filament-spatie-laravel-translatable-plugin-translations'),
            ], 'filament-spatie-laravel-translatable-plugin-translations');
        }

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-spatie-laravel-translatable-plugin');
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
        $config = $this->app['config']->get($key) ?? [];

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }
}
