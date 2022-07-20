<?php

namespace Filament\Notifications\Facades;

use Filament\Notifications\NotificationManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void send(Notification $notification)
 *
 * @see NotificationManager
 */
class Notification extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NotificationManager::class;
    }
}
