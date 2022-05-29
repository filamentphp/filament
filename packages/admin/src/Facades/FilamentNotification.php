<?php

namespace Filament\Facades;

use Filament\NotificationManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void notify(string $status, string $message)
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
