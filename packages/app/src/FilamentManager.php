<?php

namespace Filament;

use Closure;
use Exception;
use Filament\Contracts\Plugin;
use Filament\Events\ServingFilament;
use Filament\Events\TenantSet;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Support\Assets\Theme;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class FilamentManager
{
    protected ?Context $currentContext = null;

    /**
     * @var array<string, Context>
     */
    protected array $contexts = [];

    protected ?Model $tenant = null;

    protected bool $isServing = false;

    public function auth(): Guard
    {
        return $this->getCurrentContext()->auth();
    }

    public function bootCurrentContext(): void
    {
        $this->getCurrentContext()->boot();
    }

    /**
     * @return array<NavigationGroup>
     */
    public function buildNavigation(): array
    {
        return $this->getCurrentContext()->buildNavigation();
    }

    public function mountNavigation(): void
    {
        $this->getCurrentContext()->mountNavigation();
    }

    public function registerContext(Context $context): void
    {
        $this->contexts[$context->getId()] = $context;

        if ($context->isDefault()) {
            $this->setCurrentContext($context);
        }
    }

    public function serving(Closure $callback): void
    {
        Event::listen(ServingFilament::class, $callback);
    }

    public function getGlobalSearchProvider(): GlobalSearchProvider
    {
        return $this->getCurrentContext()->getGlobalSearchProvider();
    }

    public function renderHook(string $name): Htmlable
    {
        return $this->getCurrentContext()->getRenderHook($name);
    }

    public function getCurrentContext(): ?Context
    {
        return $this->currentContext ?? null;
    }

    public function getContext(?string $id = null): Context
    {
        return $this->contexts[$id] ?? $this->getDefaultContext();
    }

    public function getDefaultContext(): Context
    {
        return Arr::first(
            $this->contexts,
            fn (Context $context): bool => $context->isDefault(),
            fn () => throw new Exception('No default Filament context is set. You may do this with the `default()` method inside a Filament provider\'s `context()` configuration.'),
        );
    }

    /**
     * @return array<string, Context>
     */
    public function getContexts(): array
    {
        return $this->contexts;
    }

    public function getTenant(): ?Model
    {
        return $this->tenant;
    }

    public function getTenantModel(): ?string
    {
        return $this->getCurrentContext()->getTenantModel();
    }

    public function getTenantOwnershipRelationshipName(): string
    {
        return $this->getCurrentContext()->getTenantOwnershipRelationshipName();
    }

    public function hasTopNavigation(): bool
    {
        return $this->getCurrentContext()->hasTopNavigation();
    }

    public function getRoutableTenant(): ?Model
    {
        if (! $this->getCurrentContext()->hasRoutableTenancy()) {
            return null;
        }

        return $this->getTenant();
    }

    public function setCurrentContext(?Context $context): void
    {
        $this->currentContext = $context;
    }

    public function setTenant(?Model $tenant): void
    {
        $this->tenant = $tenant;

        if ($tenant) {
            event(new TenantSet($tenant, $this->auth()->user()));
        }
    }

    public function hasEmailVerification(): bool
    {
        return $this->getCurrentContext()->hasEmailVerification();
    }

    public function hasLogin(): bool
    {
        return $this->getCurrentContext()->hasLogin();
    }

    public function hasRegistration(): bool
    {
        return $this->getCurrentContext()->hasRegistration();
    }

    public function hasPasswordReset(): bool
    {
        return $this->getCurrentContext()->hasPasswordReset();
    }

    public function hasTenancy(): bool
    {
        return $this->getCurrentContext()->hasTenancy();
    }

    public function hasTenantBilling(): bool
    {
        return $this->getCurrentContext()->hasTenantBilling();
    }

    public function hasTenantRegistration(): bool
    {
        return $this->getCurrentContext()->hasTenantRegistration();
    }

    public function hasRoutableTenancy(): bool
    {
        return $this->getCurrentContext()->hasRoutableTenancy();
    }

    public function getAuthGuard(): string
    {
        return $this->getCurrentContext()->getAuthGuard();
    }

    public function getHomeUrl(): string
    {
        return $this->getCurrentContext()->getHomeUrl();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getEmailVerificationPromptUrl(array $parameters = []): ?string
    {
        return $this->getCurrentContext()->getEmailVerificationPromptUrl($parameters);
    }

    public function getEmailVerifiedMiddleware(): string
    {
        return $this->getCurrentContext()->getEmailVerifiedMiddleware();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getLoginUrl(array $parameters = []): ?string
    {
        return $this->getCurrentContext()->getLoginUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getRegistrationUrl(array $parameters = []): ?string
    {
        return $this->getCurrentContext()->getRegistrationUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getRequestPasswordResetUrl(array $parameters = []): ?string
    {
        return $this->getCurrentContext()->getRequestPasswordResetUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user, array $parameters = []): string
    {
        return $this->getCurrentContext()->getVerifyEmailUrl($user, $parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user, array $parameters = []): string
    {
        return $this->getCurrentContext()->getResetPasswordUrl($token, $user, $parameters);
    }

    public function getTenantBillingProvider(): ?Billing\Providers\Contracts\Provider
    {
        return $this->getCurrentContext()->getTenantBillingProvider();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantBillingUrl(array $parameters = [], ?Model $tenant = null): ?string
    {
        return $this->getCurrentContext()->getTenantBillingUrl($tenant ?? $this->getTenant(), $parameters);
    }

    public function getTenantRegistrationPage(): ?string
    {
        return $this->getCurrentContext()->getTenantRegistrationPage();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantRegistrationUrl(array $parameters = []): ?string
    {
        return $this->getCurrentContext()->getTenantRegistrationUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getLogoutUrl(array $parameters = []): string
    {
        return $this->getCurrentContext()->getLogoutUrl($parameters);
    }

    public function getMaxContentWidth(): ?string
    {
        return $this->getCurrentContext()->getMaxContentWidth();
    }

    /**
     * @return array<NavigationGroup>
     */
    public function getNavigation(): array
    {
        return $this->getCurrentContext()->getNavigation();
    }

    /**
     * @return array<string | int, NavigationGroup | string>
     */
    public function getNavigationGroups(): array
    {
        return $this->getCurrentContext()->getNavigationGroups();
    }

    /**
     * @return array<NavigationItem>
     */
    public function getNavigationItems(): array
    {
        return $this->getCurrentContext()->getNavigationItems();
    }

    /**
     * @return array<class-string>
     */
    public function getPages(): array
    {
        return $this->getCurrentContext()->getPages();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getPrimaryColor(): array
    {
        return $this->getCurrentContext()->getPrimaryColor();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getSecondaryColor(): array
    {
        return $this->getCurrentContext()->getSecondaryColor();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getGrayColor(): array
    {
        return $this->getCurrentContext()->getGrayColor();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getDangerColor(): array
    {
        return $this->getCurrentContext()->getDangerColor();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getWarningColor(): array
    {
        return $this->getCurrentContext()->getWarningColor();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getSuccessColor(): array
    {
        return $this->getCurrentContext()->getSuccessColor();
    }

    /**
     * @return array{
     *     'primary': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'secondary': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'gray': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'danger': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'warning': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'success': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     * }
     */
    public function getColors(): array
    {
        return $this->getCurrentContext()->getColors();
    }

    public function getSidebarWidth(): string
    {
        return $this->getCurrentContext()->getSidebarWidth();
    }

    public function getCollapsedSidebarWidth(): string
    {
        return $this->getCurrentContext()->getCollapsedSidebarWidth();
    }

    /**
     * @return array<class-string>
     */
    public function getResources(): array
    {
        return $this->getCurrentContext()->getResources();
    }

    /**
     * @return array<MenuItem>
     */
    public function getTenantMenuItems(): array
    {
        return $this->getCurrentContext()->getTenantMenuItems();
    }

    /**
     * @return array<MenuItem>
     */
    public function getUserMenuItems(): array
    {
        return $this->getCurrentContext()->getUserMenuItems();
    }

    public function getModelResource(string | Model $model): ?string
    {
        return $this->getCurrentContext()->getModelResource($model);
    }

    public function getTheme(): Theme
    {
        return $this->getCurrentContext()->getTheme();
    }

    public function getUrl(?Model $tenant = null): ?string
    {
        return $this->getCurrentContext()->getUrl($tenant);
    }

    public function getTenantAvatarUrl(Model $tenant): string
    {
        $avatar = null;

        if ($tenant instanceof HasAvatar) {
            $avatar = $tenant->getFilamentAvatarUrl();
        }

        if ($avatar) {
            return $avatar;
        }

        return app($this->getDefaultAvatarProvider())->get($tenant);
    }

    public function getTenantName(Model $tenant): string
    {
        if ($tenant instanceof HasName) {
            return $tenant->getFilamentName();
        }

        return $tenant->getAttributeValue('name');
    }

    public function getNameForDefaultAvatar(Model | Authenticatable $record): string
    {
        if ($this->getTenantModel() === $record::class) {
            return $this->getTenantName($record);
        }

        return $this->getUserName($record);
    }

    public function getUserAvatarUrl(Model | Authenticatable $user): string
    {
        $avatar = null;

        if ($user instanceof HasAvatar) {
            $avatar = $user->getFilamentAvatarUrl();
        }

        if ($avatar) {
            return $avatar;
        }

        return app($this->getDefaultAvatarProvider())->get($user);
    }

    public function getUserName(Model | Authenticatable $user): string
    {
        if ($user instanceof HasName) {
            return $user->getFilamentName();
        }

        return $user->getAttributeValue('name');
    }

    /**
     * @return array<Model>
     */
    public function getUserTenants(HasTenants | Model | Authenticatable $user): array
    {
        $tenants = $user->getTenants($this->getCurrentContext());

        if ($tenants instanceof Collection) {
            $tenants = $tenants->all();
        }

        return $tenants;
    }

    public function getUserDefaultTenant(HasTenants | Model | Authenticatable $user): ?Model
    {
        $tenant = null;
        $context = $this->getCurrentContext();

        if ($user instanceof HasDefaultTenant) {
            $tenant = $user->getDefaultTenant($context);
        }

        if (! $tenant) {
            $tenant = Arr::first($this->getUserTenants($user));
        }

        return $tenant;
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return $this->getCurrentContext()->getWidgets();
    }

    public function getFavicon(): ?string
    {
        return $this->getCurrentContext()->getFavicon();
    }

    public function hasDarkMode(): bool
    {
        return $this->getCurrentContext()->hasDarkMode();
    }

    public function hasDarkModeForced(): bool
    {
        return $this->getCurrentContext()->hasDarkModeForced();
    }

    public function getBrandName(): string
    {
        return $this->getCurrentContext()->getBrandName();
    }

    public function hasDatabaseNotifications(): bool
    {
        return $this->getCurrentContext()->hasDatabaseNotifications();
    }

    public function getDatabaseNotificationsPollingInterval(): ?string
    {
        return $this->getCurrentContext()->getDatabaseNotificationsPollingInterval();
    }

    public function getFontHtml(): Htmlable
    {
        return $this->getCurrentContext()->getFontHtml();
    }

    public function getFontFamily(): string
    {
        return $this->getCurrentContext()->getFontFamily();
    }

    public function getFontProvider(): string
    {
        return $this->getCurrentContext()->getFontProvider();
    }

    public function getFontUrl(): ?string
    {
        return $this->getCurrentContext()->getFontUrl();
    }

    public function getDefaultAvatarProvider(): string
    {
        return $this->getCurrentContext()->getDefaultAvatarProvider();
    }

    public function getPlugin(string $id): Plugin
    {
        return $this->getCurrentContext()->getPlugin($id);
    }

    public function isSidebarCollapsibleOnDesktop(): bool
    {
        return $this->getCurrentContext()->isSidebarCollapsibleOnDesktop();
    }

    public function isSidebarFullyCollapsibleOnDesktop(): bool
    {
        return $this->getCurrentContext()->isSidebarFullyCollapsibleOnDesktop();
    }

    public function hasCollapsibleNavigationGroups(): bool
    {
        return $this->getCurrentContext()->hasCollapsibleNavigationGroups();
    }

    public function setServingStatus(bool $condition = true): void
    {
        $this->isServing = $condition;
    }

    public function isServing(): bool
    {
        return $this->isServing;
    }
}
