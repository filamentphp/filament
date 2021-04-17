<?php

namespace Filament\JetstreamExtension;

use Filament\JetstreamExtension\Http\Livewire\NavigationMenu;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/**
 * Class JetstreamExtensionServiceProvider
 * @package Filament\JetstreamExtension
 */
class JetstreamExtensionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('jetstream.stack') === 'livewire') {
            $this->overrideConfiguration();
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootLoaders();
        $this->bootPublishing();

        if (config('jetstream.stack') === 'livewire') {
            Livewire::component('navigation-menu', NavigationMenu::class);
        }
    }

    /**
     * Overrides filament core configuration.
     *
     * @return void
     */
    protected function overrideConfiguration()
    {
        // Overrides filament path to user jetstream prefix
        $this->app['config']->set('filament.path', 'dashboard');

        // Set the auth guard back to the default to support teams
        $this->app['config']->set('filament.auth.guard', 'web');

        // Set custom page layout for this Jetstream extension
        $this->app['config']->set('filament.page_layout', 'jetstream-extension::layouts.app');
    }

    /**
     * Boot loaders for extension.
     *
     * @return void
     */
    protected function bootLoaders()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'jetstream-extension');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'jetstream-extension');
    }

    /**
     * Boot publishing for extension.
     *
     * @return void
     */
    protected function bootPublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/jetstream-extension'),
        ], 'jetstream-extension-lang');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/jetstream-extension'),
        ], 'jetstream-extension-views');
    }
}
