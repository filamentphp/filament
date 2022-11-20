<?php

namespace Filament\Notifications\Testing;

use Filament\Notifications\Notification;

/**
 * @return TestCall | TestCase | mixed
 */
function assertNotified(Notification | string $notification = null)
{
    Notification::assertNotified($notification);

    return test();
}
