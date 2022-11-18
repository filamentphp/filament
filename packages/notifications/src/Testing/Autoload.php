<?php

declare(strict_types=1);

namespace Filament\Notifications\Testing;

use Filament\Notifications\Notification;
use Pest\Plugin;

Plugin::uses(InteractsWithFilamentNotifications::class);

/**
 * @return TestCall|TestCase|mixed
 */
function assertNotified(Notification | string $notification = null)
{
    // @phpstan-ignore-next-line
    return test()->assertNotified(...func_get_args());
}
