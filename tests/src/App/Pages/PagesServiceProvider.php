<?php

namespace Filament\Tests\App\Pages;

use Filament\Support\PluginServiceProvider;
use Filament\Tests\App\Fixtures\Pages\Settings;

class PagesServiceProvider extends PluginServiceProvider
{
    public static string $name = 'pages';

    protected function getPages(): array
    {
        return [
            Settings::class,
        ];
    }
}
