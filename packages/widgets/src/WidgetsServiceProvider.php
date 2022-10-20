<?php

namespace Filament\Widgets;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\PluginServiceProvider;

class WidgetsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-widgets';

    protected function getAssetPackage(): ?string
    {
        return 'widgets';
    }

    protected function getAssets(): array
    {
        return [
            AlpineComponent::make('chart', __DIR__ . '/../dist/components/chart.js'),
        ];
    }

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

        return array_merge($commands, $aliases);
    }
}
