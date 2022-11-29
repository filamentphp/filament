<?php

namespace Filament\Widgets;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\PluginServiceProvider;

class WidgetsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-widgets';

    protected function getAssetPackage(): ?string
    {
        return 'widgets';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            AlpineComponent::make('chart', __DIR__ . '/../dist/components/chart.js'),
            AlpineComponent::make('stats-overview/card/chart', __DIR__ . '/../dist/components/stats-overview/card/chart.js'),
        ];
    }
}
