<?php

namespace Filament\Facades;

use Closure;
use Filament\Billing\Providers\Contracts\Provider as BillingProvider;
use Filament\Contracts\Plugin;
use Filament\Enums\ThemeMode;
use Filament\FilamentManager;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\Models\Contracts\HasTenants;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Support\Assets\Theme;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool arePasswordsRevealable()
 * @method static StatefulGuard auth()
 * @method static void bootCurrentPanel()
 * @method static array<NavigationGroup> buildNavigation()
 * @method static string getAuthGuard()
 * @method static string | null getAuthPasswordBroker()
 * @method static string | Htmlable getBrandName()
 * @method static string | Htmlable | null getBrandLogo()
 * @method static string | null getBrandLogoHeight()
 * @method static array getClusteredComponents(?string $cluster = null)
 * @method static string getCollapsedSidebarWidth()
 * @method static Panel | null getCurrentPanel()
 * @method static string | Htmlable | null getDarkModeBrandLogo()
 * @method static string | null getDatabaseNotificationsPollingInterval()
 * @method static string getDefaultAvatarProvider()
 * @method static Panel getDefaultPanel()
 * @method static string | null getEmailVerificationPromptUrl(array $parameters = [])
 * @method static string getEmailVerifiedMiddleware()
 * @method static string | null getFavicon()
 * @method static string getFontFamily()
 * @method static Htmlable getFontHtml()
 * @method static string getFontProvider()
 * @method static string | null getFontUrl()
 * @method static array<string> getGlobalSearchKeyBindings()
 * @method static GlobalSearchProvider | null getGlobalSearchProvider()
 * @method static string | null getHomeUrl()
 * @method static string | null getLoginUrl(array $parameters = [])
 * @method static string getLogoutUrl(array $parameters = [])
 * @method static MaxWidth | string | null getMaxContentWidth()
 * @method static string | null getModelResource(string | Model $model)
 * @method static string getNameForDefaultAvatar(Model | Authenticatable $user)
 * @method static array<NavigationGroup> getNavigation()
 * @method static array<string | int, NavigationGroup | string> getNavigationGroups()
 * @method static array<NavigationItem> getNavigationItems()
 * @method static array getPages()
 * @method static Panel getPanel(?string $id = null)
 * @method static array<string, Panel> getPanels()
 * @method static Plugin getPlugin(string $id)
 * @method static string | null getProfileUrl(array $parameters = [])
 * @method static string | null getRegistrationUrl(array $parameters = [])
 * @method static string | null getRequestPasswordResetUrl(array $parameters = [])
 * @method static string getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user, array $parameters = [])
 * @method static array getResources()
 * @method static string getSidebarWidth()
 * @method static Model | null getTenant()
 * @method static string | null getTenantAvatarUrl(Model $tenant)
 * @method static BillingProvider | null getTenantBillingProvider()
 * @method static string | null getTenantBillingUrl(array $parameters = [], Model | null $tenant = null)
 * @method static array<MenuItem> getTenantMenuItems()
 * @method static string | null getTenantModel()
 * @method static string getTenantName(Model $tenant)
 * @method static string getTenantOwnershipRelationshipName()
 * @method static string | null getTenantProfilePage()
 * @method static string | null getTenantRegistrationPage()
 * @method static string | null getTenantProfileUrl(array $parameters = [])
 * @method static string | null getTenantRegistrationUrl(array $parameters = [])
 * @method static Theme getTheme()
 * @method static ThemeMode getDefaultThemeMode()
 * @method static string | null getUserAvatarUrl(Model | Authenticatable $user)
 * @method static Model | null getUserDefaultTenant(HasTenants | Model | Authenticatable $user)
 * @method static array<MenuItem> getUserMenuItems()
 * @method static string getUserName(Model | Authenticatable $user)
 * @method static array<Model> getUserTenants(HasTenants | Model | Authenticatable $user)
 * @method static string | null getUrl(Model | null $tenant = null)
 * @method static string getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user, array $parameters = [])
 * @method static array getWidgets()
 * @method static bool hasBreadcrumbs()
 * @method static bool hasCollapsibleNavigationGroups()
 * @method static bool hasDarkMode()
 * @method static bool hasDarkModeForced()
 * @method static bool hasDatabaseNotifications()
 * @method static bool hasEmailVerification()
 * @method static bool hasLogin()
 * @method static bool hasNavigation()
 * @method static bool hasPasswordReset()
 * @method static bool hasPlugin(string $id)
 * @method static bool hasProfile()
 * @method static bool hasRegistration()
 * @method static bool hasTenancy()
 * @method static bool hasTenantBilling()
 * @method static bool hasTenantMenu()
 * @method static bool hasTenantProfile()
 * @method static bool hasTenantRegistration()
 * @method static bool hasTopNavigation()
 * @method static bool hasUnsavedChangesAlerts()
 * @method static bool isServing()
 * @method static bool isSidebarCollapsibleOnDesktop()
 * @method static bool isSidebarFullyCollapsibleOnDesktop()
 * @method static void mountNavigation()
 * @method static void serving(Closure $callback)
 * @method static void setCurrentPanel(Panel | null $panel = null)
 * @method static void setServingStatus(bool $condition = true)
 * @method static void setTenant(Model | null $tenant = null, bool $isQuiet = false)
 *
 * @see FilamentManager
 */
class Filament extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament';
    }

    public static function registerPanel(Panel | Closure $panel): void
    {
        static::getFacadeApplication()->resolving(
            static::getFacadeAccessor(),
            fn (FilamentManager $filamentManager) => $filamentManager->registerPanel(value($panel)),
        );
    }
}
