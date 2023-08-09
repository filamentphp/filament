<?php

namespace Filament\Support\Facades;

use Filament\Support\Icons\IconManager;
use Illuminate\Support\Facades\Facade;

/**
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

    /**
     * @param  array<string, string>  $icons
     */
    public static function register(array $icons): void
    {
        static::resolved(function (IconManager $iconManager) use ($icons) {
            $iconManager->register($icons);
        });
    }
}
