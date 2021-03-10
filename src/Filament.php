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
 * @method static array getWidgets()
 * @method static void registerAuthorizations(string $target, array $authorizations = [])
 * @method static void registerPage(string $page)
 * @method static void registerResource(string $resource)
 * @method static void registerRole(string $role)
 * @method static void registerWidget(string $widget)
 * @method static Resource userResource()
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
