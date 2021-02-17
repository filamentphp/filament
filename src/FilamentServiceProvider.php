<?php

namespace Filament;

use BladeUI\Icons\Factory as BladeUIFactory;
use Filament\Commands;
use Filament\Http\Middleware\AuthorizeResourceRoute;
use Filament\Models\FilamentUser;
use Filament\Providers\RouteServiceProvider;
use Filament\View\Components;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class FilamentServiceProvider extends ServiceProvider
{
    public $singletons = [
        FilamentManager::class => FilamentManager::class,
        Navigation::class => Navigation::class,
    ];

    public function boot()
    {
        $this->bootCommands();
        $this->bootDirectives();
        $this->bootLoaders();
        $this->bootLivewireComponents();
        $this->bootMiddleware();
        $this->bootPublishing();

        $this->app->booted(function () {
            $this->configure();
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filament.php', 'filament');

        $this->registerIcons();
        $this->registerProviders();
    }

    protected function bootCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Commands\MakeResourceCommand::class,
            Commands\MakeUserCommand::class,
        ]);
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
        $this->loadViewComponentsAs('filament', [
            'avatar' => Components\Avatar::class,
            'image' => Components\Image::class,
            'layouts.app' => Components\Layouts\App::class,
            'layouts.auth' => Components\Layouts\Auth::class,
            'layouts.base' => Components\Layouts\Base::class,
            'nav' => Components\Nav::class,
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filament');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filament');
    }

    protected function bootLivewireComponents()
    {
        $this->registerLivewireComponentDirectory(__DIR__ . '/Http/Livewire', 'Filament\\Http\\Livewire', 'filament.');
        $this->registerLivewireComponentDirectory(app_path('Filament/Resources'), 'App\\Filament\\Resources', 'filament.resources.');
    }

    protected function bootMiddleware()
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('filament.authorize.resource-route', AuthorizeResourceRoute::class);
    }

    protected function bootPublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../dist' => public_path('vendor/filament'),
        ], 'filament-assets');

        $this->publishes([
            __DIR__ . '/../config/filament.php' => config_path('filament.php'),
        ], 'filament-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/filament'),
        ], 'filament-lang');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/filament'),
        ], 'filament-views');
    }

    protected function configure()
    {
        $this->app['config']->set('auth.guards.filament', [
            'driver' => 'session',
            'provider' => 'filament_users',
        ]);

        $this->app['config']->set('auth.passwords.filament_users', [
            'provider' => 'filament_users',
            'table' => 'filament_password_resets',
            'expire' => 60,
            'throttle' => 60,
        ]);

        $this->app['config']->set('auth.providers.filament_users', [
            'driver' => 'eloquent',
            'model' => FilamentUser::class,
        ]);

        $this->app['config']->set('forms', [
            'default_filesystem_disk' => $this->app['config']->get('filament.default_filesystem_disk'),
        ]);

        $this->app['config']->set('forms.rich_editor', [
            'default_attachment_upload_url' => route('filament.rich-editor-attachments.upload'),
        ]);
    }

    protected function registerIcons()
    {
        $this->callAfterResolving(BladeUIFactory::class, function (BladeUIFactory $factory) {
            $factory->add('filamenticons', [
                'path' => __DIR__ . '/../resources/svg',
                'prefix' => 'filamenticon',
            ]);
        });
    }

    protected function registerProviders()
    {
        $this->app->register(RouteServiceProvider::class);
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
                return is_subclass_of($class, Component::class) && ! (new ReflectionClass($class))->isAbstract();
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
