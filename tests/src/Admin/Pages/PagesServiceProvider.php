<?php

namespace Filament\Tests\Admin\Pages;

use Filament\PluginServiceProvider;
use Filament\Tests\Admin\Fixtures\Pages\Settings;

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
