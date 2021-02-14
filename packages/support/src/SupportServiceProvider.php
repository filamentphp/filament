<?php

namespace Filament;

use Filament\Support\Providers\RouteServiceProvider;
use Filament\Support\Providers\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class SupportServiceProvider extends ServiceProvider
{
    public $singletons = [
        SupportManager::class => SupportManager::class,
    ];

    public function boot()
    {
        $this->bootAssets();
        $this->bootDirectives();
        $this->bootLoaders();
        $this->bootPublishing();
    }

    public function register()
    {
        $this->registerProviders();
    }

    protected function bootAssets()
    {
        //
    }

    protected function bootDirectives()
    {
        Blade::directive('filamentScripts', function () {
            $scripts = '';

            foreach (Support::getScripts() as $filename => $path) {
                $scripts .= '<script src="'.route('filament.asset', [
                    'filename' => $filename,
                ]).'"></script>';
            }

            return Blade::compileString("{$scripts} @stack('filament-scripts')");
        });

        Blade::directive('filamentStyles', function () {
            $styles = '';

            foreach (Support::getStyles() as $filename => $path) {
                $styles .= '<link rel="stylesheet" href="'.route('filament.asset', [
                    'filename' => $filename,
                ]).'" />';
            }

            return Blade::compileString("{$styles} @stack('filament-styles')");
        });

        Blade::directive('pushonce', function ($expression) {
            [$pushName, $pushSub] = explode(':', trim(substr($expression, 1, -1)));

            $key = '__pushonce_' . str_replace('-', '_', $pushName) . '_' . str_replace('-', '_', $pushSub);

            return "<?php if(! isset(\$__env->{$key})): \$__env->{$key} = 1; \$__env->startPush('{$pushName}'); ?>";
        });

        Blade::directive('endpushonce', function () {
            return '<?php $__env->stopPush(); endif; ?>';
        });
    }

    protected function bootLoaders()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament');
    }

    protected function bootPublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament'),
        ], 'filament-views');
    }

    protected function registerProviders()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
