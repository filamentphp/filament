<?php

namespace Filament\Facades;

use Closure;
use Filament\Billing\Providers\Contracts\Provider as BillingProvider;
use Filament\Context;
use Filament\Contracts\Plugin;
use Filament\FilamentManager;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\Models\Contracts\HasTenants;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Support\Assets\Theme;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static StatefulGuard auth()
 * @method static void bootCurrentContext()
 * @method static string getAuthGuard()
 * @method static string getBrandName()
 * @method static Context | null getCurrentContext()
 * @method static Context getContext(?string $id = null)
 * @method static array<string, Context> getContexts()
 * @method static string | null getDatabaseNotificationsPollingInterval()
 * @method static Context getDefaultContext()
 * @method static string | null getEmailVerificationPromptUrl()
 * @method static string getEmailVerifiedMiddleware()
 * @method static string | null getFavicon()
 * @method static GlobalSearchProvider getGlobalSearchProvider()
 * @method static Htmlable getFontHtml()
 * @method static string getFontFamily()
 * @method static string getFontProvider()
 * @method static string | null getFontUrl()
 * @method static string getHomeUrl()
 * @method static string | null getLoginUrl()
 * @method static string getLogoutUrl()
 * @method static string | null getRegistrationUrl()
 * @method static string | null getModelResource(string | Model $model)
 * @method static array<NavigationGroup> getNavigation()
 * @method static array<string | int, NavigationGroup | string> getNavigationGroups()
 * @method static array<NavigationItem> getNavigationItems()
 * @method static array<class-string> getPages()
 * @method static Plugin getPlugin(string $id)
 * @method static array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} getPrimaryColor()
 * @method static array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} getSecondaryColor()
 * @method static array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} getGrayColor()
 * @method static array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} getDangerColor()
 * @method static array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} getWarningColor()
 * @method static array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} getSuccessColor()
 * @method static array{
 *     'primary': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} | null,
 *     'secondary': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} | null,
 *     'gray': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} | null,
 *     'danger': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} | null,
 *     'warning': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} | null,
 *     'success': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string} | null,
 * } getColors()
 * @method static string getSidebarWidth()
 * @method static string getCollapsedSidebarWidth()
 * @method static string | null getRequestPasswordResetUrl()
 * @method static string getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user)
 * @method static array<class-string> getResources()
 * @method static Model | null getRoutableTenant()
 * @method static Model | null getTenant()
 * @method static string | null getTenantAvatarUrl(Model $tenant)
 * @method static BillingProvider | null getTenantBillingProvider()
 * @method static string | null getTenantBillingUrl(Model $tenant)
 * @method static array<MenuItem> getTenantMenuItems()
 * @method static string | null getTenantModel()
 * @method static string getTenantName(Model $tenant)
 * @method static string getTenantOwnershipRelationshipName()
 * @method static string | null getTenantRegistrationPage()
 * @method static string | null getTenantRegistrationUrl()
 * @method static Theme getTheme()
 * @method static string | null getUrl(Model | null $tenant = null)
 * @method static string getNameForDefaultAvatar(Model | Authenticatable $user)
 * @method static string | null getUserAvatarUrl(Model | Authenticatable $user)
 * @method static Model | null getUserDefaultTenant(HasTenants | Model | Authenticatable $user)
 * @method static array<MenuItem> getUserMenuItems()
 * @method static string getUserName(Model | Authenticatable $user)
 * @method static array<Model> getUserTenants(HasTenants | Model | Authenticatable $user)
 * @method static string getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user)
 * @method static array<class-string> getWidgets()
 * @method static bool hasCollapsibleNavigationGroups()
 * @method static bool hasDarkMode()
 * @method static bool hasDarkModeForced()
 * @method static bool hasDatabaseNotifications()
 * @method static bool hasEmailVerification()
 * @method static bool hasLogin()
 * @method static bool hasTenancy()
 * @method static bool hasTenantRegistration()
 * @method static bool hasTopNavigation()
 * @method static bool hasRegistration()
 * @method static bool hasRoutableTenancy()
 * @method static bool isServing()
 * @method static bool isSidebarCollapsibleOnDesktop()
 * @method static bool isSidebarFullyCollapsibleOnDesktop()
 * @method static void registerContext(Context $context)
 * @method static Htmlable renderHook(string $name)
 * @method static void serving(Closure $callback)
 * @method static void setCurrentContext(Context | null $context = null)
 * @method static void setServingStatus(bool $condition = true)
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
