<?php

namespace Filament;

use Closure;
use Exception;
use Filament\Contracts\Plugin;
use Filament\Events\ServingFilament;
use Filament\Events\TenantSet;
use Filament\Exceptions\NoDefaultContextSetException;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Support\Assets\Theme;
use Filament\Support\Facades\FilamentAsset;
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
    /**
     * @var array<string, Context>
     */
    protected array $contexts = [];

    protected ?Context $currentContext = null;

    protected bool $isServing = false;

    protected ?Model $tenant = null;

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

    public function getAuthGuard(): string
    {
        return $this->getCurrentContext()->getAuthGuard();
    }

    public function getBrandName(): string
    {
        return $this->getCurrentContext()->getBrandName();
    }

    /**
     * @return array{
     *     'danger': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'gray': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'info': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'primary': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'secondary': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'success': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     *     'warning': array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null,
     * }
     */
    public function getColors(): array
    {
        return $this->getCurrentContext()->getColors();
    }

    public function getCollapsedSidebarWidth(): string
    {
        return $this->getCurrentContext()->getCollapsedSidebarWidth();
    }

    public function getContext(?string $id = null): Context
    {
        return $this->contexts[$id] ?? $this->getDefaultContext();
    }

    public function getCurrentContext(): ?Context
    {
        return $this->currentContext ?? null;
    }

    /**
     * @return array<string, Context>
     */
    public function getContexts(): array
    {
        return $this->contexts;
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getDangerColor(): array
    {
        return $this->getCurrentContext()->getDangerColor();
    }

    public function getDatabaseNotificationsPollingInterval(): ?string
    {
        return $this->getCurrentContext()->getDatabaseNotificationsPollingInterval();
    }

    public function getDefaultAvatarProvider(): string
    {
        return $this->getCurrentContext()->getDefaultAvatarProvider();
    }

    /**
     * @throws NoDefaultContextSetException
     */
    public function getDefaultContext(): Context
    {
        return Arr::first(
            $this->contexts,
            fn (Context $context): bool => $context->isDefault(),
            fn () => throw NoDefaultContextSetException::make(),
        );
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

    public function getFavicon(): ?string
    {
        return $this->getCurrentContext()->getFavicon();
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

    public function getFontHtml(): Htmlable
    {
        return $this->getCurrentContext()->getFontHtml();
    }

    /**
     * @return array<string>
     */
    public function getGlobalSearchKeyBindings(): array
    {
        return $this->getCurrentContext()->getGlobalSearchKeyBindings();
    }

    public function getGlobalSearchProvider(): ?GlobalSearchProvider
    {
        return $this->getCurrentContext()->getGlobalSearchProvider();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getGrayColor(): array
    {
        return $this->getCurrentContext()->getGrayColor();
    }

    public function getHomeUrl(): ?string
    {
        return $this->getCurrentContext()->getHomeUrl() ?? $this->getCurrentContext()->getUrl();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getInfoColor(): array
    {
        return $this->getCurrentContext()->getInfoColor();
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
    public function getLogoutUrl(array $parameters = []): string
    {
        return $this->getCurrentContext()->getLogoutUrl($parameters);
    }

    public function getMaxContentWidth(): ?string
    {
        return $this->getCurrentContext()->getMaxContentWidth();
    }

    public function getModelResource(string | Model $model): ?string
    {
        return $this->getCurrentContext()->getModelResource($model);
    }

    public function getNameForDefaultAvatar(Model | Authenticatable $record): string
    {
        if ($this->getTenantModel() === $record::class) {
            return $this->getTenantName($record);
        }

        return $this->getUserName($record);
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

    public function getPlugin(string $id): Plugin
    {
        return $this->getCurrentContext()->getPlugin($id);
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getPrimaryColor(): array
    {
        return $this->getCurrentContext()->getPrimaryColor();
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
    public function getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user, array $parameters = []): string
    {
        return $this->getCurrentContext()->getResetPasswordUrl($token, $user, $parameters);
    }

    /**
     * @return array<class-string>
     */
    public function getResources(): array
    {
        return $this->getCurrentContext()->getResources();
    }

    public function getRoutableTenant(): ?Model
    {
        if (! $this->getCurrentContext()->hasRoutableTenancy()) {
            return null;
        }

        return $this->getTenant();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getSecondaryColor(): array
    {
        return $this->getCurrentContext()->getSecondaryColor();
    }

    public function getSidebarWidth(): string
    {
        return $this->getCurrentContext()->getSidebarWidth();
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getSuccessColor(): array
    {
        return $this->getCurrentContext()->getSuccessColor();
    }

    public function getTenant(): ?Model
    {
        return $this->tenant;
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

    /**
     * @return array<MenuItem>
     */
    public function getTenantMenuItems(): array
    {
        return $this->getCurrentContext()->getTenantMenuItems();
    }

    public function getTenantModel(): ?string
    {
        return $this->getCurrentContext()->getTenantModel();
    }

    public function getTenantName(Model $tenant): string
    {
        if ($tenant instanceof HasName) {
            return $tenant->getFilamentName();
        }

        return $tenant->getAttributeValue('name');
    }

    public function getTenantOwnershipRelationshipName(): string
    {
        return $this->getCurrentContext()->getTenantOwnershipRelationshipName();
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

    public function getTheme(): Theme
    {
        return $this->getCurrentContext()->getTheme();
    }

    public function getUserAvatarUrl(Model | Authenticatable $user): string
    {
        $avatar = null;

        if ($user instanceof HasAvatar) {
            $avatar = $user->getFilamentAvatarUrl();
        } else {
            $avatar = $user->getAttributeValue('avatar_url');
        }

        if ($avatar) {
            return $avatar;
        }

        return app($this->getDefaultAvatarProvider())->get($user);
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
     * @return array<MenuItem>
     */
    public function getUserMenuItems(): array
    {
        return $this->getCurrentContext()->getUserMenuItems();
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

    public function getUrl(?Model $tenant = null): ?string
    {
        return $this->getCurrentContext()->getUrl($tenant);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user, array $parameters = []): string
    {
        return $this->getCurrentContext()->getVerifyEmailUrl($user, $parameters);
    }

    /**
     * @return array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string}
     */
    public function getWarningColor(): array
    {
        return $this->getCurrentContext()->getWarningColor();
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return $this->getCurrentContext()->getWidgets();
    }

    public function hasBreadcrumbs(): bool
    {
        return $this->getCurrentContext()->hasBreadcrumbs();
    }

    public function hasCollapsibleNavigationGroups(): bool
    {
        return $this->getCurrentContext()->hasCollapsibleNavigationGroups();
    }

    public function hasDarkMode(): bool
    {
        return $this->getCurrentContext()->hasDarkMode();
    }

    public function hasDarkModeForced(): bool
    {
        return $this->getCurrentContext()->hasDarkModeForced();
    }

    public function hasDatabaseNotifications(): bool
    {
        return $this->getCurrentContext()->hasDatabaseNotifications();
    }

    public function hasEmailVerification(): bool
    {
        return $this->getCurrentContext()->hasEmailVerification();
    }

    public function hasLogin(): bool
    {
        return $this->getCurrentContext()->hasLogin();
    }

    public function hasNavigation(): bool
    {
        return $this->getCurrentContext()->hasNavigation();
    }

    public function hasPasswordReset(): bool
    {
        return $this->getCurrentContext()->hasPasswordReset();
    }

    public function hasRegistration(): bool
    {
        return $this->getCurrentContext()->hasRegistration();
    }

    public function hasRoutableTenancy(): bool
    {
        return $this->getCurrentContext()->hasRoutableTenancy();
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

    public function hasTopNavigation(): bool
    {
        return $this->getCurrentContext()->hasTopNavigation();
    }

    public function isServing(): bool
    {
        return $this->isServing;
    }

    public function isSidebarCollapsibleOnDesktop(): bool
    {
        return $this->getCurrentContext()->isSidebarCollapsibleOnDesktop();
    }

    public function isSidebarFullyCollapsibleOnDesktop(): bool
    {
        return $this->getCurrentContext()->isSidebarFullyCollapsibleOnDesktop();
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

    public function renderHook(string $name): Htmlable
    {
        return $this->getCurrentContext()->getRenderHook($name);
    }

    public function serving(Closure $callback): void
    {
        Event::listen(ServingFilament::class, $callback);
    }

    public function setCurrentContext(?Context $context): void
    {
        $this->currentContext = $context;
    }

    public function setServingStatus(bool $condition = true): void
    {
        $this->isServing = $condition;
    }

    public function setTenant(?Model $tenant): void
    {
        $this->tenant = $tenant;

        if ($tenant) {
            event(new TenantSet($tenant, $this->auth()->user()));
        }
    }

    /**
     * @deprecated Use the `navigationGroups()` method on the context configuration instead.
     *
     * @param  array<string | int, NavigationGroup | string>  $groups
     */
    public function registerNavigationGroups(array $groups): void
    {
        try {
            $this->getDefaultContext()->navigationGroups($groups);
        } catch (NoDefaultContextSetException $exception) {
            throw new Exception('Please use the `navigationGroups()` method on the context configuration to register navigation groups.');
        }
    }

    /**
     * @deprecated Use the `navigationItems()` method on the context configuration instead.
     *
     * @param  array<NavigationItem>  $items
     */
    public function registerNavigationItems(array $items): void
    {
        try {
            $this->getDefaultContext()->navigationItems($items);
        } catch (NoDefaultContextSetException $exception) {
            throw new Exception('Please use the `navigationItems()` method on the context configuration to register navigation items.');
        }
    }

    /**
     * @deprecated Use the `pages()` method on the context configuration instead.
     *
     * @param  array<class-string>  $pages
     */
    public function registerPages(array $pages): void
    {
        try {
            $this->getDefaultContext()->pages($pages);
        } catch (NoDefaultContextSetException $exception) {
            throw new Exception('Please use the `pages()` method on the context configuration to register pages.');
        }
    }

    public function registerRenderHook(string $name, Closure $callback): void
    {
        try {
            $this->getDefaultContext()->renderHook($name, $callback);
        } catch (NoDefaultContextSetException $exception) {
            throw new Exception('Please use the `renderHook()` method on the context configuration to register render hooks.');
        }
    }

    /**
     * @deprecated Use the `resources()` method on the context configuration instead.
     *
     * @param  array<class-string>  $resources
     */
    public function registerResources(array $resources): void
    {
        try {
            $this->getDefaultContext()->resources($resources);
        } catch (NoDefaultContextSetException $exception) {
            throw new Exception('Please use the `resources()` method on the context configuration to register resources.');
        }
    }

    /**
     * @deprecated Register scripts using the `FilamentAsset` facade instead.
     *
     * @param  array<mixed>  $scripts
     */
    public function registerScripts(array $scripts, bool $shouldBeLoadedBeforeCoreScripts = false): void
    {
        throw new Exception('Please use the `FilamentAsset` facade to register scripts.');
    }

    /**
     * @deprecated Register script data using the `FilamentAsset` facade instead.
     *
     * @param  array<string, mixed>  $data
     */
    public function registerScriptData(array $data): void
    {
        FilamentAsset::registerScriptData($data);
    }

    /**
     * @deprecated Register styles using the `FilamentAsset` facade instead.
     *
     * @param  array<mixed>  $styles
     */
    public function registerStyles(array $styles): void
    {
        throw new Exception('Please use the `FilamentAsset` facade to register styles.');
    }

    /**
     * @deprecated Use the `theme()` method on the context configuration instead.
     */
    public function registerTheme(string | Htmlable | null $theme): void
    {
        try {
            $this->getDefaultContext()->theme($theme);
        } catch (NoDefaultContextSetException $exception) {
            throw new Exception('Please use the `theme()` method on the context configuration to register themes.');
        }
    }

    /**
     * @deprecated Use the `viteTheme()` method on the context configuration instead.
     *
     * @param  string | array<string>  $theme
     */
    public function registerViteTheme(string | array $theme, ?string $buildDirectory = null): void
    {
        try {
            $this->getDefaultContext()->viteTheme($theme, $buildDirectory);
        } catch (NoDefaultContextSetException $exception) {
            throw new Exception('Please use the `viteTheme()` method on the context configuration to register themes.');
        }
    }

    /**
     * @deprecated Use the `userMenuItems()` method on the context configuration instead.
     *
     * @param  array<MenuItem>  $items
     */
    public function registerUserMenuItems(array $items): void
    {
        try {
            $this->getDefaultContext()->userMenuItems($items);
        } catch (NoDefaultContextSetException $exception) {
            throw new Exception('Please use the `userMenuItems()` method on the context configuration to register user menu items.');
        }
    }

    /**
     * @deprecated Use the `widgets()` method on the context configuration instead.
     *
     * @param  array<class-string>  $widgets
     */
    public function registerWidgets(array $widgets): void
    {
        try {
            $this->getDefaultContext()->widgets($widgets);
        } catch (NoDefaultContextSetException $exception) {
            throw new Exception('Please use the `widgets()` method on the context configuration to register widgets.');
        }
    }
}
