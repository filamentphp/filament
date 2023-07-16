<?php

namespace Filament\Support\Facades;

use Closure;
use Filament\Support\View\ViewManager;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void registerRenderHook(string $name, Closure $hook)
 * @method static Htmlable renderHook(string $name)
 *
 * @see ViewManager
 */
class FilamentView extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ViewManager::class;
    }
}
