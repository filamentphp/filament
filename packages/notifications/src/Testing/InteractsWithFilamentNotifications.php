<?php

declare(strict_types=1);

namespace Filament\Notifications\Testing;

use Filament\Notifications\Notification;

trait InteractsWithFilamentNotifications
{
    public function assertNotified(Notification | string $notification = null)
    {
        Notification::assertNotified($notification);

        return $this;
    }
}
