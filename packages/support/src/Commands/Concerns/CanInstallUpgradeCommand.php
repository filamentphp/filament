<?php

namespace Filament\Support\Commands\Concerns;

trait CanInstallUpgradeCommand
{
    protected function installUpgradeCommand(): void
    {
        $path = base_path('composer.json');

        if (! file_exists($path)) {
            return;
        }

        $configuration = json_decode(file_get_contents($path), associative: true);

        $command = '@php artisan filament:upgrade';

        if (in_array($command, $configuration['scripts']['post-update-cmd'] ?? [])) {
            return;
        }

        $configuration['scripts']['post-update-cmd'] ??= [];
        $configuration['scripts']['post-update-cmd'][] = $command;

        file_put_contents(
            $path,
            json_encode($configuration, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL,
        );
    }
}
