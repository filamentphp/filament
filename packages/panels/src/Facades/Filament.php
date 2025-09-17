<?php

namespace Filament\Facades;

use Closure;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Billing\Providers\Contracts\BillingProvider;
use Filament\Contracts\Plugin;
use Filament\Enums\ThemeMode;
use Filament\FilamentManager;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Filament\Models\Contracts\HasTenants;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Support\Assets\Theme;
use Filament\Support\Enums\Width;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Livewire\Component;

/**
 * @method static bool arePasswordsRevealable()
 * @method static StatefulGuard auth()
 * @method static void bootCurrentPanel()
 * @method static array<NavigationGroup> buildNavigation()
 * @method static void currentDomain(?string $domain)
 * @method static string getAuthGuard()
 * @method static string | null getAuthPasswordBroker()
 * @method static string | Htmlable getBrandName()
 * @method static string | Htmlable | null getBrandLogo()
 * @method static string | null getBrandLogoHeight()
 * @method static array<string | int, array<class-string> | class-string> getClusteredComponents(?string $cluster = null)
 * @method static string getCollapsedSidebarWidth()
 * @method static string getCurrentDomain(?string $testingDomain = null)
 * @method static Panel | null getCurrentPanel()
 * @method static Panel | null getCurrentOrDefaultPanel()
 * @method static string | Htmlable | null getDarkModeBrandLogo()
 * @method static string | null getDatabaseNotificationsPollingInterval()
 * @method static string getDefaultAvatarProvider()
 * @method static Panel getDefaultPanel()
 * @method static string | null getEmailVerificationPromptUrl(array<string, mixed> $parameters = [])
 * @method static string | null getSetUpRequiredMultiFactorAuthenticationUrl(array<string, mixed> $parameters = [])
 * @method static string getEmailVerifiedMiddleware()
 * @method static array<array{ title: string | Closure, body: string | Closure | null }> getErrorNotifications()
 * @method static string | null getFavicon()
 * @method static string getFontFamily()
 * @method static string getMonoFontFamily()
 * @method static string getSerifFontFamily()
 * @method static Htmlable getFontHtml()
 * @method static Htmlable getMonoFontHtml()
 * @method static Htmlable getSerifFontHtml()
 * @method static string getFontProvider()
 * @method static string getMonoFontProvider()
 * @method static string getSerifFontProvider()
 * @method static string | null getFontUrl()
 * @method static string | null getMonoFontUrl()
 * @method static string | null getSerifFontUrl()
 * @method static string getGlobalSearchDebounce()
 * @method static array<string> getGlobalSearchKeyBindings()
 * @method static GlobalSearchProvider | null getGlobalSearchProvider()
 * @method static string | null getHomeUrl()
 * @method static string | null getLoginUrl(array<string, mixed> $parameters = [])
 * @method static string getLogoutUrl(array<string, mixed> $parameters = [])
 * @method static Width | string | null getMaxContentWidth()
 * @method static string | null getModelResource(string | Model $model)
 * @method static array<string, MultiFactorAuthenticationProvider> getMultiFactorAuthenticationProviders()
 * @method static string getNameForDefaultAvatar(Model | Authenticatable $user)
 * @method static array<NavigationGroup> getNavigation()
 * @method static array<string | int, NavigationGroup | string> getNavigationGroups()
 * @method static array<NavigationItem> getNavigationItems()
 * @method static array<class-string> getPages()
 * @method static Panel getPanel(?string $id = null, bool $isStrict = true)
 * @method static array<string, Panel> getPanels()
 * @method static Plugin getPlugin(string $id)
 * @method static string | null getProfileUrl(array<string, mixed> $parameters = [])
 * @method static string | null getRegistrationUrl(array<string, mixed> $parameters = [])
 * @method static string | null getRequestPasswordResetUrl(array<string, mixed> $parameters = [])
 * @method static string getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user, array<string, mixed> $parameters = [])
 * @method static array<class-string> getResources()
 * @method static string getResourceUrl(string | Model $model, string $name = 'index', array<string, mixed> $parameters = [], bool $isAbsolute = false, ?Model $tenant = null)
 * @method static ?string getResourceCreatePageRedirect()
 * @method static ?string getResourceEditPageRedirect()
 * @method static class-string<Component> getSidebarLivewireComponent()
 * @method static string getSidebarWidth()
 * @method static SubNavigationPosition getSubNavigationPosition()
 * @method static string getTenancyScopeName()
 * @method static Model | null getTenant()
 * @method static string | null getTenantAvatarUrl(Model $tenant)
 * @method static BillingProvider | null getTenantBillingProvider()
 * @method static string | null getTenantBillingUrl(array<string, mixed> $parameters = [], Model | null $tenant = null)
 * @method static array<Action> getTenantMenuItems()
 * @method static string | null getTenantModel()
 * @method static string getTenantName(Model $tenant)
 * @method static string getTenantOwnershipRelationshipName()
 * @method static string | null getTenantProfilePage()
 * @method static string | null getTenantRegistrationPage()
 * @method static string | null getTenantProfileUrl(array<string, mixed> $parameters = [])
 * @method static string | null getTenantRegistrationUrl(array<string, mixed> $parameters = [])
 * @method static Theme getTheme()
 * @method static class-string<Component> getTopbarLivewireComponent()
 * @method static ThemeMode getDefaultThemeMode()
 * @method static string | null getUserAvatarUrl(Model | Authenticatable $user)
 * @method static Model | null getUserDefaultTenant(HasTenants | Model | Authenticatable $user)
 * @method static array<Action> getUserMenuItems()
 * @method static string getUserName(Model | Authenticatable $user)
 * @method static array<Model> getUserTenants(HasTenants | Model | Authenticatable $user)
 * @method static string | null getUrl(Model | null $tenant = null)
 * @method static string getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user, array<string, mixed> $parameters = [])
 * @method static string getVerifyEmailChangeUrl(MustVerifyEmail | Model | Authenticatable $user, string $newEmail, array<string, mixed> $parameters = [])
 * @method static string getBlockEmailChangeVerificationUrl(MustVerifyEmail | Model | Authenticatable $user, string $newEmail, string $verificationSignature, array<string, mixed> $parameters = [])
 * @method static array<class-string<Widget>> getWidgets()
 * @method static bool hasBreadcrumbs()
 * @method static bool hasCollapsibleNavigationGroups()
 * @method static bool hasDarkMode()
 * @method static bool hasDarkModeForced()
 * @method static bool hasDatabaseNotifications()
 * @method static bool hasLazyLoadedDatabaseNotifications()
 * @method static bool hasEmailChangeVerification()
 * @method static bool hasEmailVerification()
 * @method static bool hasErrorNotifications()
 * @method static bool hasLogin()
 * @method static bool hasMultiFactorAuthentication()
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
 * @method static bool hasTopbar()
 * @method static bool hasUserMenu()
 * @method static bool hasTopNavigation()
 * @method static bool hasUnsavedChangesAlerts()
 * @method static bool isAuthorizationStrict()
 * @method static bool isProfilePageSimple()
 * @method static bool isServing()
 * @method static bool isSidebarCollapsibleOnDesktop()
 * @method static bool isSidebarFullyCollapsibleOnDesktop()
 * @method static void serving(Closure $callback)
 * @method static void setCurrentPanel(Panel | string | null $panel = null)
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
            PanelRegistry::class,
            fn (PanelRegistry $registry) => $registry->register(value($panel)),
        );
    }
}
