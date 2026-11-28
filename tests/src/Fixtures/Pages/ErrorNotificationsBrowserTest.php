<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ErrorNotificationsBrowserTest extends Page
{
    protected string $view = 'pages.error-notifications-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public int $savedCount = 0;

    public function save(): void
    {
        $this->savedCount++;

        Notification::make()
            ->title('Changes saved')
            ->success()
            ->persistent()
            ->send();
    }

    public function fail(int $status): void
    {
        abort($status);
    }
}
