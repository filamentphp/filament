<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DatabaseNotificationsBrowserTest extends Page
{
    protected string $view = 'pages.database-notifications-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBell;

    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        DatabaseNotifications::trigger('database-notifications-browser-test-trigger');
    }
}
