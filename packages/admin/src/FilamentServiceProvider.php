<?php

namespace Filament;

use Filament\Facades\Filament;
use Filament\Facades\FilamentNotification;
use Filament\Http\Livewire\Auth\Login;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\MirrorConfigToSubpackages;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Http\Responses\Auth\LogoutResponse;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Actions\BulkAction as TableBulkAction;
use Filament\Tables\Actions\ButtonAction;
use Filament\Tables\Actions\IconButtonAction;
use Filament\Testing\TestsPages;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Widgets\Widget;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Testing\TestableLivewire;
use ReflectionClass;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Finder\SplFileInfo;

class FilamentServiceProvider extends PackageServiceProvider
{
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
            Commands\UpgradeCommand::class,
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
        $this->app->resolving('filament', function (): void {
            $this->discoverPages();
            $this->discoverResources();
            $this->discoverWidgets();
        });

        $this->app->scoped('filament', function (): FilamentManager {
            return new FilamentManager();
        });

        $this->app->scoped(NotificationManager::class, function (): NotificationManager {
            return new NotificationManager();
        });

        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);

        $this->mergeConfigFrom(__DIR__ . '/../config/filament.php', 'filament');
    }

    public function packageBooted(): void
    {
        $this->bootLivewireComponents();

        $this->bootTableActionConfiguration();

        TestableLivewire::mixin(new TestsPages());
    }

    protected function bootLivewireComponents(): void
    {
        Livewire::addPersistentMiddleware([
            DispatchServingFilamentEvent::class,
            MirrorConfigToSubpackages::class,
        ]);

        Livewire::listen('component.dehydrate', [FilamentNotification::class, 'handleLivewireResponses']);

        Livewire::component('filament.core.auth.login', Login::class);
        Livewire::component('filament.core.global-search', GlobalSearch::class);
        Livewire::component('filament.core.pages.dashboard', Dashboard::class);
        Livewire::component('filament.core.widgets.account-widget', AccountWidget::class);
        Livewire::component('filament.core.widgets.filament-info-widget', FilamentInfoWidget::class);

        $this->registerLivewireComponentDirectory(config('filament.livewire.path'), config('filament.livewire.namespace'), 'filament.');
    }

    protected function bootTableActionConfiguration(): void
    {
        Filament::serving(function (): void {
            $notify = function (string $status, string $message): void {
                Filament::notify($status, $message);
            };

            TableAction::configureUsing(function (TableAction $action) use ($notify): TableAction {
                match (config('filament.layout.tables.actions.type')) {
                    ButtonAction::class => $action->button(),
                    IconButtonAction::class => $action->iconButton(),
                    default => null,
                };

                $action->notifyUsing($notify);

                return $action;
            });

            TableBulkAction::configureUsing(function (TableBulkAction $action) use ($notify): TableBulkAction {
                $action->notifyUsing($notify);

                return $action;
            });
        });
    }

    protected function discoverPages(): void
    {
        $filesystem = app(Filesystem::class);

        Filament::registerPages(config('filament.pages.register', []));

        if (! $filesystem->exists(config('filament.pages.path'))) {
            return;
        }

        Filament::registerPages(collect($filesystem->allFiles(config('filament.pages.path')))
            ->map(function (SplFileInfo $file): string {
                return (string) Str::of(config('filament.pages.namespace'))
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(fn (string $class): bool => is_subclass_of($class, Page::class) && (! (new ReflectionClass($class))->isAbstract()))
            ->toArray());
    }

    protected function discoverResources(): void
    {
        $filesystem = app(Filesystem::class);

        Filament::registerResources(config('filament.resources.register', []));

        if (! $filesystem->exists(config('filament.resources.path'))) {
            return;
        }

        Filament::registerResources(collect($filesystem->allFiles(config('filament.resources.path')))
            ->map(function (SplFileInfo $file): string {
                return (string) Str::of(config('filament.resources.namespace'))
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(fn (string $class): bool => is_subclass_of($class, Resource::class) && (! (new ReflectionClass($class))->isAbstract()))
            ->toArray());
    }

    protected function discoverWidgets(): void
    {
        $filesystem = app(Filesystem::class);

        Filament::registerWidgets(config('filament.widgets.register', []));

        if (! $filesystem->exists(config('filament.widgets.path'))) {
            return;
        }

        Filament::registerWidgets(collect($filesystem->allFiles(config('filament.widgets.path')))
            ->map(function (SplFileInfo $file): string {
                return (string) Str::of(config('filament.widgets.namespace'))
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(fn ($class): bool => is_subclass_of($class, Widget::class) && (! (new ReflectionClass($class))->isAbstract()))
            ->toArray());
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
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }

    protected function registerLivewireComponentDirectory(string $directory, string $namespace, string $aliasPrefix = ''): void
    {
        $filesystem = app(Filesystem::class);

        if (! $filesystem->isDirectory($directory)) {
            return;
        }

        collect($filesystem->allFiles($directory))
            ->map(function (SplFileInfo $file) use ($namespace): string {
                return (string) Str::of($namespace)
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(fn (string $class): bool => is_subclass_of($class, Component::class) && (! (new ReflectionClass($class))->isAbstract()))
            ->each(function (string $class) use ($namespace, $aliasPrefix): void {
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
