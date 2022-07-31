<?php

namespace Filament;

use Filament\Resources\RelationManagers\RelationGroup;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use ReflectionClass;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\Finder\SplFileInfo;

abstract class PluginServiceProvider extends PackageServiceProvider
{
    public static string $name;

    protected array $pages = [];

    protected array $relationManagers = [];

    protected array $resources = [];

    protected array $beforeCoreScripts = [];

    protected array $scripts = [];

    protected array $styles = [];

    protected array $widgets = [];

    public function configurePackage(Package $package): void
    {
        $this->packageConfiguring($package);

        $package
            ->name(static::$name)
            ->hasCommands($this->getCommands());

        $configFileName = $package->shortName();

        if (file_exists($this->package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($this->package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($this->package->basePath('/../resources/views'))) {
            $package->hasViews();
        }

        $this->packageConfigured($package);
    }

    public function packageConfiguring(Package $package): void
    {
    }

    public function packageConfigured(Package $package): void
    {
    }

    public function packageRegistered(): void
    {
        $this->app->resolving('filament', function () {
            Facades\Filament::registerPages($this->getPages());
            Facades\Filament::registerResources($this->getResources());
            Facades\Filament::registerWidgets($this->getWidgets());

            Facades\Filament::serving(function () {
                Facades\Filament::registerUserMenuItems($this->getUserMenuItems());
                Facades\Filament::registerScripts($this->getBeforeCoreScripts(), true);
                Facades\Filament::registerScripts($this->getScripts());
                Facades\Filament::registerStyles($this->getStyles());
                Facades\Filament::registerScriptData($this->getScriptData());
            });
        });
    }

    public function packageBooted(): void
    {
        foreach ($this->getPages() as $page) {
            Livewire::component($page::getName(), $page);
        }

        foreach ($this->getRelationManagers() as $manager) {
            if ($manager instanceof RelationGroup) {
                foreach ($manager->getManagers() as $groupedManager) {
                    $this->registerRelationManager($groupedManager);
                }

                return;
            }

            $this->registerRelationManager($manager);
        }

        foreach ($this->getResources() as $resource) {
            foreach ($resource::getPages() as $page) {
                Livewire::component($page['class']::getName(), $page['class']);
            }

            foreach ($resource::getRelations() as $relation) {
                if ($relation instanceof RelationGroup) {
                    foreach ($relation->getManagers() as $groupedRelation) {
                        Livewire::component($groupedRelation::getName(), $groupedRelation);
                    }

                    continue;
                }

                Livewire::component($relation::getName(), $relation);
            }

            foreach ($resource::getWidgets() as $widget) {
                Livewire::component($widget::getName(), $widget);
            }
        }

        foreach ($this->getWidgets() as $widget) {
            Livewire::component($widget::getName(), $widget);
        }

        $this->registerMacros();
    }

    protected function registerRelationManager(string $manager): void
    {
        Livewire::component($manager::getName(), $manager);
    }

    protected function getCommands(): array
    {
        return [];
    }

    protected function getPages(): array
    {
        return $this->pages;
    }

    protected function getRelationManagers(): array
    {
        return $this->relationManagers;
    }

    protected function getResources(): array
    {
        return $this->resources;
    }

    protected function getScriptData(): array
    {
        return [];
    }

    protected function getBeforeCoreScripts(): array
    {
        return $this->beforeCoreScripts;
    }

    protected function getScripts(): array
    {
        return $this->scripts;
    }

    protected function getStyles(): array
    {
        return $this->styles;
    }

    protected function getUserMenuItems(): array
    {
        return [];
    }

    protected function getWidgets(): array
    {
        return $this->widgets;
    }

    protected function registerMacros(): void
    {
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
