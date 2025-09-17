<?php

namespace Filament\Support\Facades;

use Filament\Support\TimezoneManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void set(?string $timezone)
 * @method static string get()
 *
 * @see TimezoneManager
 */
class FilamentTimezone extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TimezoneManager::class;
    }
}
