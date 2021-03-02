<?php

namespace Filament;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void registerPage(string $page)
 * @method static void registerResource(string $resource) 
 * @method static void registerRole(string $role)
 * @method static void registerWidget(string $widget)
 *
 * @see \Filament\FilamentManager
 */
class Filament extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FilamentManager::class;
    }
}
