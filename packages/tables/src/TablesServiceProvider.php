<?php

namespace Filament;

use Illuminate\Support\ServiceProvider;

class TablesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->bootLoaders();
        $this->bootPublishing();
    }

    protected function bootLoaders()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tables');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'tables');
    }

    protected function bootPublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/tables'),
        ], 'tables-lang');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/tables'),
        ], 'tables-views');
    }
}
