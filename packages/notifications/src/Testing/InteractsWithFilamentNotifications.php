<?php

declare(strict_types=1);

namespace Filament\Notifications\Testing;

use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert;

trait InteractsWithFilamentNotifications
{
    public function assertNotified(Notification | string $notification = null)
    {
        $notifications = session()->get('filament.notifications');

        Assert::assertIsArray($notifications);

        $expectedNotification = Arr::last($notifications);

        Assert::assertIsArray($expectedNotification);

        if ($notification instanceof Notification) {
            Assert::assertSame($expectedNotification, $notification->toArray());
        } elseif (filled($notification)) {
            Assert::assertSame($expectedNotification['title'], $notification);
        }

        return $this;
    }
}
