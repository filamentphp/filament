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
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\ButtonAction;
use Filament\Tables\Actions\IconButtonAction;
use Filament\Testing\TestsPageActions;
use Filament\Widgets\Widget;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;
use ReflectionClass;
use Spatie\LaravelPackageTools\Package;
use Symfony\Component\Finder\SplFileInfo;

class FilamentServiceProvider extends PluginServiceProvider
{
    protected array $livewireComponents = [];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament')
            ->hasCommands($this->getCommands())
            ->hasConfigFile()
            ->hasRoutes(['web'])
            ->hasTranslations()
            ->hasViews();
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
            Commands\MakeWidgetCommand::class,
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

        $this->bootLivewireComponents();

        $this->bootTableActionConfiguration();

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }

        Filament::serving(function () {
            Filament::setServingStatus();
        });

        TestableLivewire::mixin(new TestsPageActions());
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
            $fileClass = (string) Str::of($namespace)
                ->append('\\', $file->getRelativePathname())
                ->replace(['/', '.php'], ['\\', '']);

            if (! class_exists($fileClass)) {
                continue;
            }

            if ((new ReflectionClass($fileClass))->isAbstract()) {
                continue;
            }

            $filePath = Str::of($directory . '/' . $file->getRelativePathname());

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

            $livewireAlias = Str::of($fileClass)
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

        if (Str::of($directory)->startsWith(config('filament.livewire.path'))) {
            return;
        }

        $filesystem = app(Filesystem::class);

        if ((! $filesystem->exists($directory)) && (! Str::of($directory)->contains('*'))) {
            return;
        }

        $namespace = Str::of($namespace);

        $register = array_merge(
            $register,
            collect($filesystem->allFiles($directory))
                ->map(function (SplFileInfo $file) use ($namespace): string {
                    $variableNamespace = $namespace->contains('*') ? str_ireplace(
                        ['\\' . $namespace->before('*'), $namespace->after('*')],
                        ['', ''],
                        Str::of($file->getPath())
                            ->after(base_path())
                            ->replace(['/'], ['\\']),
                    ) : null;

                    if (is_string($variableNamespace)) {
                        $variableNamespace = (string) Str::of($variableNamespace)->before('\\');
                    }

                    return (string) $namespace
                        ->append('\\', $file->getRelativePathname())
                        ->replace('*', $variableNamespace)
                        ->replace(['/', '.php'], ['\\', '']);
                })
                ->filter(fn (string $class): bool => is_subclass_of($class, $baseClass) && (! (new ReflectionClass($class))->isAbstract()))
                ->all(),
        );
    }

    protected function bootLivewireComponents(): void
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

    protected function bootTableActionConfiguration(): void
    {
        Filament::serving(function (): void {
            TableAction::configureUsing(function (TableAction $action): TableAction {
                match (config('filament.layout.tables.actions.type')) {
                    ButtonAction::class => $action->button(),
                    IconButtonAction::class => $action->iconButton(),
                    default => null,
                };

                return $action;
            });
        });
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
