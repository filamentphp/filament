<?php

namespace Filament\Support\Facades;

use Closure;
use Filament\Support\Colors\ColorManager;
use Filament\Support\View\Components\Contracts\HasColorMap;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, array<int | string, string | int>> getColors()
 * @method static ?array<int | string, string | int> getColor(string $color)
 * @method static array<string> getComponentClasses(class-string<HasColorMap> | HasColorMap $component, ?string $color)
 * @method static array<string> getComponentCustomStyles(class-string<HasColorMap> | HasColorMap $component, array<string> $color)
 * @method static void addShades(string $alias, array<int> $shades)
 * @method static array<int> | null getAddedShades(string $alias)
 * @method static array<int> | null getOverridingShades(string $alias)
 * @method static array<int> | null getRemovedShades(string $alias)
 * @method static void overrideShades(string $alias, array<int> $shades)
 * @method static void removeShades(string $alias, array<int> $shades)
 *
 * @see ColorManager
 */
class FilamentColor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ColorManager::class;
    }

    /**
     * @param  array<string, array<int | string, string | int> | string> | Closure  $colors
     */
    public static function register(array | Closure $colors): void
    {
        static::resolved(function (ColorManager $colorManager) use ($colors): void {
            $colorManager->register($colors);
        });
    }
}
