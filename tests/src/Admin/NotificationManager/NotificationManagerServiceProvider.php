<?php

namespace Filament\Tests\Admin\NotificationManager;

use Filament\PluginServiceProvider;
use Filament\Tests\Admin\Fixtures\Pages\Settings;

class NotificationManagerServiceProvider extends PluginServiceProvider
{
    public static string $name = 'notification-manager';

    protected function getPages(): array
    {
        return [
            Settings::class,
        ];
    }
}
