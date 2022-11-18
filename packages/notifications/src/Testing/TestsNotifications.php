<?php

namespace Filament\Notifications\Testing;

use Closure;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\Testing\TestableLivewire;

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
            Notification::assertNotified($notification);

            return $this;
        };
    }
}
