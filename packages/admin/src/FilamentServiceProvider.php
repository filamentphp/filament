<?php

namespace Filament;

use Filament\Facades\Filament;
use Filament\Http\Livewire\Auth\Login;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Widgets\Widget;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use ReflectionClass;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use SplFileInfo;

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
            Commands\MakePageCommand::class,
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
        $this->app->singleton('filament', function (): FilamentManager {
            return new FilamentManager();
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/filament.php', 'filament');

        $this->discoverPages();
        $this->discoverResources();
        $this->discoverWidgets();
    }

    public function packageBooted(): void
    {
        $this->bootLivewireComponents();
    }

    protected function bootLivewireComponents(): void
    {
        Livewire::component('filament.core.auth.login', Login::class);
        Livewire::component('filament.core.global-search', GlobalSearch::class);
        Livewire::component('filament.core.pages.dashboard', Dashboard::class);
        Livewire::component('filament.core.widgets.account-widget', AccountWidget::class);
        Livewire::component('filament.core.widgets.filament-info-widget', FilamentInfoWidget::class);

        $this->registerLivewireComponentDirectory(app_path('Filament'), 'App\\Filament', 'filament.');
    }

    protected function discoverPages(): void
    {
        $filesystem = new Filesystem();

        $filesystem->ensureDirectoryExists(config('filament.pages.path'));

        Filament::registerPages(config('filament.pages.register', []));

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
        $filesystem = new Filesystem();

        $filesystem->ensureDirectoryExists(config('filament.resources.path'));

        Filament::registerResources(config('filament.resources.register', []));

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
        $filesystem = new Filesystem();

        $filesystem->ensureDirectoryExists(config('filament.widgets.path'));

        Filament::registerWidgets(config('filament.widgets.register', []));

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
        $filesystem = new Filesystem();

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
