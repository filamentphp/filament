<?php

namespace Filament\Support\Facades;

use Closure;
use Filament\Support\TimezoneManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void set(string | Closure | null $timezone)
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
