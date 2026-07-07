<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;

class SpatieLaravelTagsPluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament-spatie-laravel-tags-plugin');
    }
}
