<?php

namespace Filament\Facades;

use Closure;
use Filament\Billing\Providers\Contracts\Provider as BillingProvider;
use Filament\Context;
use Filament\FilamentManager;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static StatefulGuard auth()
 * @method static void bootCurrentContext()
 * @method static string getAuthGuard()
 * @method static Context | null getCurrentContext()
 * @method static Context | null getContext(string $id = 'default')
 * @method static array getContexts()
 * @method static GlobalSearchProvider getGlobalSearchProvider()
 * @method static string getHomeUrl()
 * @method static string | null getLoginUrl()
 * @method static string getLogoutUrl()
 * @method static array getPages()
 * @method static string | null getModelResource(string | Model $model)
 * @method static array getNavigation()
 * @method static array getNavigationGroups()
 * @method static array getNavigationItems()
 * @method static array getResources()
 * @method static Model | null getRoutableTenant()
 * @method static Model | null getTenant()
 * @method static string | null getTenantAvatarUrl(Model $tenant)
 * @method static BillingProvider | null getTenantBillingProvider()
 * @method static string | null getTenantBillingUrl(Model $tenant)
 * @method static array getTenantMenuItems()
 * @method static string | null getTenantModel()
 * @method static string getTenantName(Model $tenant)
 * @method static string | null getTenantRegistrationPage()
 * @method static string | null getTenantRegistrationUrl()
 * @method static string | Htmlable | null getTheme()
 * @method static string | null getUrl(Model | null $tenant = null)
 * @method static string | null getUserAvatarUrl(Model | Authenticatable $user)
 * @method static Model | null getUserDefaultTenant(HasTenants | Model | Authenticatable $user)
 * @method static array getUserMenuItems()
 * @method static string getUserName(Model | Authenticatable $user)
 * @method static array getUserTenants(HasTenants | Model | Authenticatable $user)
 * @method static array getWidgets()
 * @method static void globalSearchProvider(string $provider, string $context = 'default')
 * @method static bool hasLogin()
 * @method static bool hasTenancy()
 * @method static bool hasTenantRegistration()
 * @method static bool hasRoutableTenancy()
 * @method static void navigation(Closure $builder, string $context = 'default')
 * @method static void registerContext(Context $context)
 * @method static void registerNavigationGroups(array $groups, string $context = 'default')
 * @method static void registerNavigationItems(array $items, string $context = 'default')
 * @method static void registerPages(array $pages, string $context = 'default')
 * @method static void registerRenderHook(string $name, Closure $callback, string $context = 'default')
 * @method static void registerResources(array $resources, string $context = 'default')
 * @method static void registerTenantMenuItems(array $items, string $context = 'default')
 * @method static void registerTheme(string | Htmlable | null $theme, string $context = 'default')
 * @method static void registerUserMenuItems(array $items, string $context = 'default')
 * @method static void registerWidgets(array $widgets, string $context = 'default')
 * @method static Htmlable renderHook(string $name)
 * @method static void serving(Closure $callback)
 * @method static void setCurrentContext(Context | null $context = null)
 * @method static void setTenant(Model | null $tenant = null)
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
