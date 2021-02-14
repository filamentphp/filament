<?php

namespace Filament;

use Filament\Support\Providers\RouteServiceProvider;
use Filament\Support\Providers\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class SupportServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->bootDirectives();
        $this->bootLoaders();
        $this->bootPublishing();
    }

    protected function bootDirectives()
    {
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
}
