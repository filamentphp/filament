<?php

namespace Filament;

use Filament\Facades\Filament;
use Filament\Http\Livewire\Auth\Login;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Http\Livewire\Notifications;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\MirrorConfigToSubpackages;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Http\Responses\Auth\LogoutResponse;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\PluginServiceProvider;
use Filament\Widgets\Widget;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class FilamentServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament';

    protected array $livewireComponents = [];

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->booting(function () {
            $this->registerComponents();
        });

        $this->app->scoped('filament', function (): FilamentManager {
            return new FilamentManager();
        });

        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);

        $this->mergeConfigFrom(__DIR__ . '/../config/filament.php', 'filament');
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        $this->registerLivewire();

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }
    }

    protected function getAssetPackage(): ?string
    {
        return null;
    }

    protected function getAssets(): array
    {
        return [
            Css::make('app', __DIR__ . '/../dist/index.css')->html(Filament::getTheme()),
            Js::make('app', __DIR__ . '/../dist/index.js')->core(),
            Js::make('echo', __DIR__ . '/../dist/echo.js')->core(),
        ];
    }

    protected function registerComponents(): void
    {
        $this->pages = config('filament.pages.register') ?? [];
        $this->resources = config('filament.resources.register') ?? [];
        $this->widgets = config('filament.widgets.register') ?? [];

        $directory = config('filament.livewire.path');
        $namespace = config('filament.livewire.namespace');

        $this->registerComponentsFromDirectory(
            Page::class,
            $this->pages,
            config('filament.pages.path'),
            config('filament.pages.namespace'),
        );

        $this->registerComponentsFromDirectory(
            Resource::class,
            $this->resources,
            config('filament.resources.path'),
            config('filament.resources.namespace'),
        );

        $this->registerComponentsFromDirectory(
            Widget::class,
            $this->widgets,
            config('filament.widgets.path'),
            config('filament.widgets.namespace'),
        );

        $filesystem = app(Filesystem::class);

        if (! $filesystem->isDirectory($directory)) {
            return;
        }

        foreach ($filesystem->allFiles($directory) as $file) {
            $fileClass = (string) str($namespace)
                ->append('\\', $file->getRelativePathname())
                ->replace(['/', '.php'], ['\\', '']);

            if (! class_exists($fileClass)) {
                continue;
            }

            if ((new ReflectionClass($fileClass))->isAbstract()) {
                continue;
            }

            $filePath = str($directory . '/' . $file->getRelativePathname());

            if ($filePath->startsWith(config('filament.resources.path')) && is_subclass_of($fileClass, Resource::class)) {
                $this->resources[] = $fileClass;

                continue;
            }

            if ($filePath->startsWith(config('filament.pages.path')) && is_subclass_of($fileClass, Page::class)) {
                $this->pages[] = $fileClass;

                continue;
            }

            if ($filePath->startsWith(config('filament.widgets.path')) && is_subclass_of($fileClass, Widget::class)) {
                $this->widgets[] = $fileClass;

                continue;
            }

            if (is_subclass_of($fileClass, RelationManager::class)) {
                continue;
            }

            if (! is_subclass_of($fileClass, Component::class)) {
                continue;
            }

            $livewireAlias = str($fileClass)
                ->after($namespace . '\\')
                ->replace(['/', '\\'], '.')
                ->prepend('filament.')
                ->explode('.')
                ->map([Str::class, 'kebab'])
                ->implode('.');

            $this->livewireComponents[$livewireAlias] = $fileClass;
        }
    }

    protected function registerComponentsFromDirectory(string $baseClass, array &$register, ?string $directory, ?string $namespace): void
    {
        if (blank($directory) || blank($namespace)) {
            return;
        }

        if (str($directory)->startsWith(config('filament.livewire.path'))) {
            return;
        }

        $filesystem = app(Filesystem::class);

        if ((! $filesystem->exists($directory)) && (! str($directory)->contains('*'))) {
            return;
        }

        $namespace = str($namespace);

        $register = array_merge(
            $register,
            collect($filesystem->allFiles($directory))
                ->map(function (SplFileInfo $file) use ($namespace): string {
                    $variableNamespace = $namespace->contains('*') ? str_ireplace(
                        ['\\' . $namespace->before('*'), $namespace->after('*')],
                        ['', ''],
                        str($file->getPath())
                            ->after(base_path())
                            ->replace(['/'], ['\\']),
                    ) : null;

                    return (string) $namespace
                        ->append('\\', $file->getRelativePathname())
                        ->replace('*', $variableNamespace)
                        ->replace(['/', '.php'], ['\\', '']);
                })
                ->filter(fn (string $class): bool => is_subclass_of($class, $baseClass) && (! (new ReflectionClass($class))->isAbstract()))
                ->all(),
        );
    }

    protected function registerLivewire(): void
    {
        Livewire::addPersistentMiddleware([
            Authenticate::class,
            DispatchServingFilamentEvent::class,
            MirrorConfigToSubpackages::class,
        ]);

        foreach (array_merge($this->livewireComponents, [
            'filament.core.auth.login' => Login::class,
            'filament.core.global-search' => GlobalSearch::class,
            'filament.core.notifications' => Notifications::class,
        ]) as $alias => $class) {
            Livewire::component($alias, $class);
        }
    }

    protected function getRoutes(): array
    {
        return ['web'];
    }

    protected function getCommands(): array
    {
        $commands = [
            Commands\MakeBelongsToManyCommand::class,
            Commands\MakeHasManyCommand::class,
            Commands\MakeHasManyThroughCommand::class,
            Commands\MakeMorphManyCommand::class,
            Commands\MakeMorphToManyCommand::class,
            Commands\MakePageCommand::class,
            Commands\MakeRelationManagerCommand::class,
            Commands\MakeResourceCommand::class,
            Commands\MakeUserCommand::class,
        ];

        $aliases = [];

        foreach ($commands as $command) {
            $class = 'Filament\\Commands\\Aliases\\' . class_basename($command);

            if (! class_exists($class)) {
                continue;
            }

            $aliases[] = $class;
        }

        return array_merge($commands, $aliases);
    }

    protected function mergeConfig(array $original, array $merging): array
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

            if ($key === 'middleware' || $key === 'register') {
                continue;
            }

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }

    protected function mergeConfigFrom($path, $key): void
    {
        $config = $this->app['config']->get($key) ?? [];

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }
}
