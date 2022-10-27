<?php

namespace Filament\Support;

use Filament\Facades\Filament;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\IconManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

abstract class PluginServiceProvider extends PackageServiceProvider
{
    public static string $name;

    public static ?string $viewNamespace = null;

    protected string $context = 'default';

    protected array $pages = [];

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
            Filament::registerPages($this->getPages(), $this->getContext());
            Filament::registerResources($this->getResources(), $this->getContext());
            Filament::registerWidgets($this->getWidgets(), $this->getContext());

            Filament::serving(function () {
                if (Filament::getCurrentContext()->getId() !== $this->getContext()) {
                    return;
                }

                Filament::registerUserMenuItems($this->getUserMenuItems(), $this->getContext());
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
        $this->registerMacros();
    }

    public function getContext(): string
    {
        return $this->context;
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
