<?php

namespace Filament\Facades;

use Closure;
use Filament\FilamentManager;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static StatefulGuard auth()
 * @method static array getBeforeCoreScripts()
 * @method static GlobalSearchProvider getGlobalSearchProvider()
 * @method static array getPages()
 * @method static string | null getModelResource(string | Model $model)
 * @method static array getNavigation()
 * @method static array getNavigationGroups()
 * @method static array getNavigationItems()
 * @method static array getResources()
 * @method static array getScripts()
 * @method static array getScriptData()
 * @method static array getStyles()
 * @method static Htmlable getThemeLink()
 * @method static string | null getUrl()
 * @method static string | null getUserAvatarUrl(Authenticatable $user)
 * @method static array getUserMenuItems()
 * @method static string getUserName(Authenticatable $user)
 * @method static array getWidgets()
 * @method static void globalSearchProvider(string $provider)
 * @method static void navigation(\Closure $builder)
 * @method static void notify(string $status, string $message, bool $isAfterRedirect = false)
 * @method static void registerNavigationGroups(array $groups)
 * @method static void registerNavigationItems(array $items)
 * @method static void registerPages(array $pages)
 * @method static void registerRenderHook(string $name, \Closure $callback)
 * @method static void registerResources(array $resources)
 * @method static void registerScripts(array $scripts, bool $shouldBeLoadedBeforeCoreScripts = false)
 * @method static void registerScriptData(array $data)
 * @method static void registerStyles(array $styles)
 * @method static void registerTheme(string | Htmlable | null $theme)
 * @method static void registerUserMenuItems(array $items)
 * @method static void registerWidgets(array $widgets)
 * @method static Htmlable renderHook(string $name)
 * @method static void serving(Closure $callback)
 *
 * @see FilamentManager
 */
class Filament extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament';
    }
}
