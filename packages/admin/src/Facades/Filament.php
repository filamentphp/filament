<?php

namespace Filament\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getAvatar()
 * @method static array getPages()
 * @method static array getNavigation()
 * @method static array getNavigationGroups()
 * @method static array getNavigationItems()
 * @method static array getResources()
 * @method static array getScripts()
 * @method static array getScriptData()
 * @method static array getStyles()
 * @method static array getWidgets()
 * @method static void registerNavigationGroups(array $groups)
 * @method static void registerNavigationItems(array $items)
 * @method static void registerPages(array $pages)
 * @method static void registerResources(array $resources)
 * @method static void registerScripts(array $scripts)
 * @method static void registerScriptData(array $data)
 * @method static void registerStyles(array $styles)
 * @method static void registerWidgets(array $widgets)
 *
 * @see \Filament\FilamentManager
 */
class Filament extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament';
    }
}
