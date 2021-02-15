<?php

namespace Filament;

use Filament\Support\Providers\ServiceProvider;
use Filament\Support\RegistersLivewireComponentDirectories;

class FormsServiceProvider extends ServiceProvider
{
    use RegistersLivewireComponentDirectories;

    public function boot()
    {
        $this->bootLoaders();
        $this->bootLivewireComponents();
        $this->bootPublishing();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/forms.php', 'forms');
    }

    protected function bootLoaders()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'forms');
    }

    protected function bootLivewireComponents()
    {
        $this->registerLivewireComponentDirectory(__DIR__ . '/Http/Livewire', 'Filament\\Forms\\Http\\Livewire', 'filament.forms.');
    }

    protected function bootPublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/forms.php' => config_path('forms.php'),
        ], 'forms-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/filament'),
        ], 'forms-lang');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament'),
        ], 'forms-views');
    }
}
