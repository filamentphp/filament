<?php

namespace Filament;

use Filament\Forms\Providers\RouteServiceProvider;
use Filament\Support\Providers\ServiceProvider;
use Filament\Support\RegistersLivewireComponentDirectories;
use Illuminate\Support\Facades\Blade;

class FormsServiceProvider extends ServiceProvider
{
    use RegistersLivewireComponentDirectories;

    public $singletons = [
        FormsManager::class => FormsManager::class,
    ];

    public function boot()
    {
        $this->bootDirectives();
        $this->bootLoaders();
        $this->bootLivewireComponents();
        $this->bootPublishing();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/forms.php', 'forms');

        $this->registerProviders();
    }

    protected function bootDirectives()
    {
        Blade::directive('formsStyles', function () {
            return '{!! \Filament\Forms::styles() !!}';
        });

        Blade::directive('formsScripts', function () {
            return '{!! \Filament\Forms::scripts() !!}';
        });
    }

    protected function bootLoaders()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament');
    }

    protected function bootLivewireComponents()
    {
        $this->registerLivewireComponentDirectory(__DIR__ . '/Http/Livewire', 'Filament\\Http\\Livewire', 'filament.');
    }

    protected function bootPublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../dist' => public_path('vendor/filament'),
        ], 'forms-assets');

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

    protected function registerProviders()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
