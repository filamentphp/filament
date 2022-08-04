<?php

namespace Filament\Facades;

use Closure;
use Filament\SpatieLaravelTranslatablePluginManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void getLocaleLabelUsing(?Closure $callback)
 * @method static ?string getLocaleLabel(string $locale)
 *
 * @see SpatieLaravelTranslatablePluginManager
 */
class SpatieLaravelTranslatablePlugin extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament-spatie-laravel-translatable-plugin';
    }
}
