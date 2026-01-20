<?php

namespace Filament\Support\Facades;

use Closure;
use Filament\Support\Grid\GridManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void defaultBreakpoint(string | Closure | null $breakpoint)
 * @method static string getDefaultBreakpoint()
 *
 * @see GridManager
 */
class FilamentGrid extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GridManager::class;
    }
}
