<?php

namespace Filament\Support\Facades;

use Filament\Support\Icons\IconManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(array $icons)
 * @method static string | null resolve(string $name)
 *
 * @see IconManager
 */
class FilamentIcon extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return IconManager::class;
    }
}
