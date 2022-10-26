<?php

namespace Filament\Support;

use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\IconManager;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

abstract class PluginServiceProvider extends PackageServiceProvider
{
    public static string $name;

    public static string $context = 'default';

    public static ?string $viewNamespace = null;

    protected array $pages = [];

    protected array $relationManagers = [];

    protected array $resources = [];

    protected array $widgets = [];

    public function configurePackage(Package $package): void
    {
        $this->packageConfiguring($package);

        $package
            ->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasRoutes($this->getRoutes());

        $configFileName = $package->shortName();

        if (file_exists($this->package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($this->package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($this->package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
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
        $this->app->afterResolving('filament', function () {
            Filament::registerPages($this->getPages(), static::$context);
            Filament::registerResources($this->getResources(), static::$context);
            Filament::registerWidgets($this->getWidgets(), static::$context);

            Filament::serving(function () {
                if (Filament::getCurrentContext()->getId() !== static::$context) {
                    return;
                }

                Filament::registerUserMenuItems($this->getUserMenuItems(), static::$context);
            });
        });

        $this->app->resolving(AssetManager::class, function () {
            FilamentAsset::register($this->getAssets(), $this->getAssetPackage());
            FilamentAsset::registerScriptData($this->getScriptData(), $this->getAssetPackage());
        });

        $this->app->resolving(IconManager::class, function () {
            FilamentIcon::register($this->getIcons());
        });
    }

    public function packageBooted(): void
    {
        Filament::serving(function () {
            if (Filament::getCurrentContext()->getId() !== static::$context) {
                return;
            }

            foreach ($this->getPages() as $page) {
                $this->registerLivewireComponent($page);
            }

            foreach ($this->getRelationManagers() as $manager) {
                if ($manager instanceof RelationGroup) {
                    foreach ($manager->getManagers() as $groupedManager) {
                        $this->registerLivewireComponent($groupedManager);
                    }

                    return;
                }

                $this->registerLivewireComponent($manager);
            }

            foreach ($this->getResources() as $resource) {
                foreach ($resource::getPages() as $page) {
                    $this->registerLivewireComponent($page['class']);
                }

                foreach ($resource::getRelations() as $relation) {
                    if ($relation instanceof RelationGroup) {
                        foreach ($relation->getManagers() as $groupedRelation) {
                            $this->registerLivewireComponent($groupedRelation);
                        }

                        continue;
                    }

                    $this->registerLivewireComponent($relation);
                }

                foreach ($resource::getWidgets() as $widget) {
                    $this->registerLivewireComponent($widget);
                }
            }

            foreach ($this->getWidgets() as $widget) {
                $this->registerLivewireComponent($widget);
            }
        });

        $this->registerMacros();
    }

    protected function registerLivewireComponent(string $component): void
    {
        Livewire::component($component::getName(), $component);
    }

    protected function getAssetPackage(): ?string
    {
        return static::$name ?? null;
    }

    protected function getAssets(): array
    {
        return [];
    }

    protected function getCommands(): array
    {
        return [];
    }

    protected function getIcons(): array
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

    protected function getRoutes(): array
    {
        return [];
    }

    protected function getScriptData(): array
    {
        return [];
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
}
