<?php

namespace Filament\Theme;

use Filament\Support\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class ThemeServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-theme';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasAssets();
    }
}
