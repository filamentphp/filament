<?php

namespace Filament\JetstreamExtension;

use App\View\Components\AppLayout;
use Filament\JetstreamExtension\Http\Livewire\NavigationMenu;
use Filament\JetstreamExtension\View\Components\AppLayout as AppLayoutExtended;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
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
        $this->app->afterResolving(BladeCompiler::class, function () {
            if (config('jetstream.stack') === 'livewire' && class_exists(Livewire::class)) {
                //Livewire::component('filament.core.dashboard', NavigationMenu::class);
                //Livewire::component('navigation-menu::override', NavigationMenu::class);
            }
        });

        $this->app->bind(AppLayout::class, AppLayoutExtended::class);
        $this->overrideConfiguration();
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

        Livewire::component('navigation-menu', NavigationMenu::class);
        //Livewire::component('filament.core.resources.pages.page::override', NavigationMenu::class);
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
