<?php

namespace Filament\Widgets;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WidgetsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-widgets')
            ->hasCommands($this->getCommands())
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

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        $commands = [
            Commands\MakeWidgetCommand::class,
        ];

        $aliases = [];

        foreach ($commands as $command) {
            $class = 'Filament\\Widgets\\Commands\\Aliases\\' . class_basename($command);

            if (! class_exists($class)) {
                continue;
            }

            $aliases[] = $class;
        }

        return [
            ...$commands,
            ...$aliases,
        ];
    }
}
