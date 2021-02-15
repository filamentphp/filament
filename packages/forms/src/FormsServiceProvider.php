<?php

namespace Filament;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use Symfony\Component\Finder\SplFileInfo;

class FormsServiceProvider extends ServiceProvider
{
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'forms');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'forms');
    }

    protected function bootLivewireComponents()
    {
        $this->registerLivewireComponentDirectory(__DIR__ . '/Http/Livewire', 'Filament\\Forms\\Http\\Livewire', 'forms.');
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
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/forms'),
        ], 'forms-lang');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/forms'),
        ], 'forms-views');
    }

    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }

    protected function mergeConfig(array $original, array $merging)
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

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }

    protected function registerLivewireComponentDirectory($directory, $namespace, $aliasPrefix = '')
    {
        $filesystem = new Filesystem();

        if (! $filesystem->isDirectory($directory)) return;

        collect($filesystem->allFiles($directory))
            ->map(function (SplFileInfo $file) use ($namespace) {
                return (string) Str::of($namespace)
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Component::class) && ! (new \ReflectionClass($class))->isAbstract();
            })
            ->each(function ($class) use ($namespace, $aliasPrefix) {
                $alias = Str::of($class)
                    ->after($namespace . '\\')
                    ->replace(['/', '\\'], '.')
                    ->prepend($aliasPrefix)
                    ->explode('.')
                    ->map([Str::class, 'kebab'])
                    ->implode('.');

                Livewire::component($alias, $class);
            });
    }
}
