<?php

namespace Filament\Widgets;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Filesystem\Filesystem;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WidgetsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-widgets')
            ->hasCommands([
                Commands\MakeWidgetCommand::class,
            ])
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            AlpineComponent::make('chart', __DIR__ . '/../dist/components/chart.js'),
            AlpineComponent::make('stats-overview/stat/chart', __DIR__ . '/../dist/components/stats-overview/stat/chart.js'),
        ], 'filament/widgets');

        if ($this->app->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament/{$file->getFilename()}"),
                ], 'filament-stubs');
            }
        }
    }
}
