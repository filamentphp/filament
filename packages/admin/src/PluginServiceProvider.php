<?php

namespace Filament;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

abstract class PluginServiceProvider extends PackageServiceProvider
{
    public static string $name;

    protected array $pages = [];

    protected array $resources = [];

    protected array $scripts = [];

    protected array $styles = [];

    protected array $widgets = [];

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasTranslations()
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->singletonIf(
            'filament',
            fn (): FilamentManager => new FilamentManager()
        );

        Facades\Filament::registerPages($this->getPages());
        Facades\Filament::registerResources($this->getResources());
        Facades\Filament::registerWidgets($this->getWidgets());

        Facades\Filament::serving(function () {
            Facades\Filament::registerScripts($this->getScripts());
            Facades\Filament::registerStyles($this->getStyles());
            Facades\Filament::registerScriptData($this->getScriptData());
        });
    }

    public function packageBooted(): void
    {
        foreach ($this->getPages() as $page) {
            Livewire::component($page::getName(), $page);
        }

        foreach ($this->getResources() as $resource) {
            foreach ($resource::getPages() as $page) {
                Livewire::component($page['class']::getName(), $page['class']);
            }

            foreach ($resource::getRelations() as $relation) {
                Livewire::component($relation::getName(), $relation);
            }

            foreach ($resource::getWidgets() as $widget) {
                Livewire::component($widget::getName(), $widget);
            }
        }

        foreach ($this->getWidgets() as $widget) {
            Livewire::component($widget::getName(), $widget);
        }
    }

    protected function getPages(): array
    {
        return $this->pages;
    }

    protected function getResources(): array
    {
        return $this->resources;
    }

    protected function getScriptData(): array
    {
        return [];
    }

    protected function getScripts(): array
    {
        return $this->scripts;
    }

    protected function getStyles(): array
    {
        return $this->styles;
    }

    protected function getWidgets(): array
    {
        return $this->widgets;
    }
}
