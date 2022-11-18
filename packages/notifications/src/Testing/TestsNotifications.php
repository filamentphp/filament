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
    use InteractsWithFilamentNotifications {
        assertNotified as filamentAssertNotified;
    }

    public function assertNotified(): Closure
    {
        return function (Notification | string $notification = null): static {
            return $this->filamentAssertNotified(...func_get_args());
        };
    }
}
