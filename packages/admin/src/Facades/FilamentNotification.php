<?php

namespace Filament\Facades;

use Filament\Notifications\Notification;
use Filament\Notifications\NotificationManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void notify(Notification | string $notification, string $message)
 *
 * @see NotificationManager
 */
class FilamentNotification extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NotificationManager::class;
    }
}
