<?php

namespace Filament\Notifications\Testing;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Livewire\Component;
use Livewire\Testing\TestableLivewire;
use PHPUnit\Framework\Assert;

/**
 * @method Component instance()
 *
 * @mixin TestableLivewire
 */
class TestsNotifications
{
    public function assertNotified(): Closure
    {
        return function (Notification | string $notification = null): static {
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
        };
    }
}
