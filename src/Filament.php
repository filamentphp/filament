<?php

namespace Filament;

use Filament\Resources\Resource;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Facade;

/**
 * @method static StatefulGuard auth()
 * @method static bool can(string $action, object|string $target)
 * @method static array getAuthorizations()
 * @method static array getPages()
 * @method static array getResources()
 * @method static array getRoles()
 * @method static array getScripts()
 * @method static array getScriptData()
 * @method static array getStyles()
 * @method static array getWidgets()
 * @method static void provideToScript(array $variables)
 * @method static void registerAuthorizations(string $target, array $authorizations = [])
 * @method static void registerPage(string $page)
 * @method static void registerResource(string $resource)
 * @method static void registerRole(string $role)
 * @method static void registerWidget(string $widget)
 * @method static void registerScript(string $name, string $path)
 * @method static void registerStyle(string $name, string $path)
 * @method static void serving($callback)
 * @method static Resource userResource()
 * @method static void ignoreMigrations()
 * @method static bool shouldRunMigrations()
 * @method static string|null version()
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
