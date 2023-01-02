<?php

namespace Filament\Widgets;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WidgetsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-widgets')
            ->hasViews();
    }

    public function packageRegistered(): void
    {
        $this->app->resolving(AssetManager::class, function () {
            FilamentAsset::register([
                AlpineComponent::make('chart', __DIR__ . '/../dist/components/chart.js'),
                AlpineComponent::make('stats-overview/card/chart', __DIR__ . '/../dist/components/stats-overview/card/chart.js'),
            ], 'filament/widgets');
        });
    }
}
