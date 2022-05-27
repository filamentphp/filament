<?php

namespace Filament\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void notify(string $status, string $message)
 *
 * @see \Filament\NotificationManager
 */
class NotificationManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Filament\NotificationManager::class;
    }
}
