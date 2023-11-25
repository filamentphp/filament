<?php

namespace Filament\Support\Facades;

use Closure;
use Filament\Support\View\ViewManager;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool hasSpaMode()
 * @method static Htmlable renderHook(string $name, string | array | null $scopes = null)
 *
 * @see ViewManager
 */
class FilamentView extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ViewManager::class;
    }

    /**
     * @param  string | array<string> | null  $scopes
     */
    public static function registerRenderHook(string $name, Closure $hook, string | array | null $scopes = null): void
    {
        static::resolved(function (ViewManager $viewManager) use ($name, $hook, $scopes) {
            $viewManager->registerRenderHook($name, $hook, $scopes);
        });
    }

    public static function spa(bool $condition = true): void
    {
        static::resolved(function (ViewManager $viewManager) use ($condition) {
            $viewManager->spa($condition);
        });
    }
}
