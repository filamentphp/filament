<?php

namespace Filament\Notifications\Testing;

use Closure;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Features\SupportTesting\Testable;

/**
 * @method Component instance()
 *
 * @mixin Testable
 */
class TestsNotifications
{
    public function assertNotified(): Closure
    {
        return function (Notification | string | null $notification = null): static {
            Notification::assertNotified($notification);

            return $this;
        };
    }
}
