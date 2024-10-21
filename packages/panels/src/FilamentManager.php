<?php

namespace Filament;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Enums\ThemeMode;
use Filament\Events\ServingFilament;
use Filament\Events\TenantSet;
use Filament\Exceptions\NoDefaultPanelSetException;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Assets\Theme;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets\Widget;
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
    protected ?string $currentDomain = null;

    protected ?Panel $currentPanel = null;

    protected bool $isServing = false;

    protected bool $isCurrentPanelBooted = false;

    protected ?Model $tenant = null;

    public function __construct()
    {
        // Resolve the panel registry to set the current panel
        // as the default, which uses a `resolving()` callback.
        app()->resolved(PanelRegistry::class) || app(PanelRegistry::class);
    }

    public function auth(): Guard
    {
        return $this->getCurrentPanel()->auth();
    }

    public function bootCurrentPanel(): void
    {
        if ($this->isCurrentPanelBooted) {
            return;
        }

        $this->getCurrentPanel()->boot();

        $this->isCurrentPanelBooted = true;
    }

    /**
     * @return array<NavigationGroup>
     */
    public function buildNavigation(): array
    {
        return $this->getCurrentPanel()->buildNavigation();
    }

    public function getAuthGuard(): string
    {
        return $this->getCurrentPanel()->getAuthGuard();
    }

    public function getAuthPasswordBroker(): ?string
    {
        return $this->getCurrentPanel()->getAuthPasswordBroker();
    }

    public function getBrandName(): string | Htmlable
    {
        return $this->getCurrentPanel()->getBrandName();
    }

    public function getBrandLogo(): string | Htmlable | null
    {
        return $this->getCurrentPanel()->getBrandLogo();
    }

    public function getBrandLogoHeight(): ?string
    {
        return $this->getCurrentPanel()->getBrandLogoHeight();
    }

    public function getCollapsedSidebarWidth(): string
    {
        return $this->getCurrentPanel()->getCollapsedSidebarWidth();
    }

    public function getCurrentPanel(): ?Panel
    {
        return $this->currentPanel;
    }

    public function getDarkModeBrandLogo(): string | Htmlable | null
    {
        return $this->getCurrentPanel()->getDarkModeBrandLogo();
    }

    public function getDatabaseNotificationsPollingInterval(): ?string
    {
        return $this->getCurrentPanel()->getDatabaseNotificationsPollingInterval();
    }

    public function getDefaultAvatarProvider(): string
    {
        return $this->getCurrentPanel()->getDefaultAvatarProvider();
    }

    /**
     * @throws NoDefaultPanelSetException
     */
    public function getDefaultPanel(): Panel
    {
        return app(PanelRegistry::class)->getDefault();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getEmailVerificationPromptUrl(array $parameters = []): ?string
    {
        return $this->getCurrentPanel()->getEmailVerificationPromptUrl($parameters);
    }

    public function getEmailVerifiedMiddleware(): string
    {
        return $this->getCurrentPanel()->getEmailVerifiedMiddleware();
    }

    public function getFavicon(): ?string
    {
        return $this->getCurrentPanel()->getFavicon();
    }

    public function getFontFamily(): string
    {
        return $this->getCurrentPanel()->getFontFamily();
    }

    public function getMonoFontFamily(): string
    {
        return $this->getCurrentPanel()->getMonoFontFamily();
    }

    public function getSerifFontFamily(): string
    {
        return $this->getCurrentPanel()->getSerifFontFamily();
    }

    public function getFontHtml(): Htmlable
    {
        return $this->getCurrentPanel()->getFontHtml();
    }

    public function getMonoFontHtml(): Htmlable
    {
        return $this->getCurrentPanel()->getMonoFontHtml();
    }

    public function getSerifFontHtml(): Htmlable
    {
        return $this->getCurrentPanel()->getSerifFontHtml();
    }

    public function getFontProvider(): string
    {
        return $this->getCurrentPanel()->getFontProvider();
    }

    public function getMonoFontProvider(): string
    {
        return $this->getCurrentPanel()->getMonoFontProvider();
    }

    public function getSerifFontProvider(): string
    {
        return $this->getCurrentPanel()->getSerifFontProvider();
    }

    public function getFontUrl(): ?string
    {
        return $this->getCurrentPanel()->getFontUrl();
    }

    public function getMonoFontUrl(): ?string
    {
        return $this->getCurrentPanel()->getMonoFontUrl();
    }

    public function getSerifFontUrl(): ?string
    {
        return $this->getCurrentPanel()->getSerifFontUrl();
    }

    public function getGlobalSearchDebounce(): string
    {
        return $this->getCurrentPanel()->getGlobalSearchDebounce();
    }

    /**
     * @return array<string>
     */
    public function getGlobalSearchKeyBindings(): array
    {
        return $this->getCurrentPanel()->getGlobalSearchKeyBindings();
    }

    public function getGlobalSearchFieldSuffix(): ?string
    {
        return $this->getCurrentPanel()->getGlobalSearchFieldSuffix();
    }

    public function getGlobalSearchProvider(): ?GlobalSearchProvider
    {
        return $this->getCurrentPanel()->getGlobalSearchProvider();
    }

    public function getHomeUrl(): ?string
    {
        return $this->getCurrentPanel()->getHomeUrl() ?? $this->getCurrentPanel()->getUrl();
    }

    public function getId(): ?string
    {
        return $this->getCurrentPanel()?->getId();
    }

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return $this->getCurrentPanel()?->getSubNavigationPosition();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getLoginUrl(array $parameters = []): ?string
    {
        return $this->getCurrentPanel()->getLoginUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getLogoutUrl(array $parameters = []): string
    {
        return $this->getCurrentPanel()->getLogoutUrl($parameters);
    }

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return $this->getCurrentPanel()->getMaxContentWidth();
    }

    /**
     * @param  class-string<Model>|Model  $model
     */
    public function getModelResource(string | Model $model): ?string
    {
        return $this->getCurrentPanel()->getModelResource($model);
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
        return $this->getCurrentPanel()->getNavigation();
    }

    /**
     * @return array<string | int, NavigationGroup | string>
     */
    public function getNavigationGroups(): array
    {
        return $this->getCurrentPanel()->getNavigationGroups();
    }

    /**
     * @return array<NavigationItem>
     */
    public function getNavigationItems(): array
    {
        return $this->getCurrentPanel()->getNavigationItems();
    }

    /**
     * @return array<string | int, array<class-string> | class-string>
     */
    public function getClusteredComponents(?string $cluster): array
    {
        return $this->getCurrentPanel()->getClusteredComponents($cluster);
    }

    /**
     * @return array<class-string>
     */
    public function getPages(): array
    {
        return $this->getCurrentPanel()->getPages();
    }

    public function getPanel(?string $id = null, bool $isStrict = true): Panel
    {
        return app(PanelRegistry::class)->get($id, $isStrict);
    }

    /**
     * @return array<string, Panel>
     */
    public function getPanels(): array
    {
        return app(PanelRegistry::class)->all();
    }

    public function getPlugin(string $id): Plugin
    {
        return $this->getCurrentPanel()->getPlugin($id);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getProfileUrl(array $parameters = []): ?string
    {
        return $this->getCurrentPanel()->getProfileUrl($parameters);
    }

    public function isProfilePageSimple(): bool
    {
        return $this->getCurrentPanel()->isProfilePageSimple();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getRegistrationUrl(array $parameters = []): ?string
    {
        return $this->getCurrentPanel()->getRegistrationUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getRequestPasswordResetUrl(array $parameters = []): ?string
    {
        return $this->getCurrentPanel()->getRequestPasswordResetUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user, array $parameters = []): string
    {
        return $this->getCurrentPanel()->getResetPasswordUrl($token, $user, $parameters);
    }

    /**
     * @return array<class-string>
     */
    public function getResources(): array
    {
        return $this->getCurrentPanel()->getResources();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getResourceUrl(string | Model $model, string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?Model $tenant = null): string
    {
        return $this->getCurrentPanel()->getResourceUrl($model, $name, $parameters, $isAbsolute, $tenant);
    }

    public function getSidebarWidth(): string
    {
        return $this->getCurrentPanel()->getSidebarWidth();
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
        return $this->getCurrentPanel()->getTenantBillingProvider();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantBillingUrl(array $parameters = [], ?Model $tenant = null): ?string
    {
        return $this->getCurrentPanel()->getTenantBillingUrl($tenant ?? $this->getTenant(), $parameters);
    }

    /**
     * @return array<Action>
     */
    public function getTenantMenuItems(): array
    {
        return $this->getCurrentPanel()->getTenantMenuItems();
    }

    /**
     * @return class-string<Model>|null
     */
    public function getTenantModel(): ?string
    {
        return $this->getCurrentPanel()->getTenantModel();
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
        return $this->getCurrentPanel()->getTenantOwnershipRelationshipName();
    }

    public function getProfilePage(): ?string
    {
        return $this->getCurrentPanel()->getProfilePage();
    }

    public function getTenantProfilePage(): ?string
    {
        return $this->getCurrentPanel()->getTenantProfilePage();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantProfileUrl(array $parameters = []): ?string
    {
        $parameters['tenant'] ??= $this->getTenant();

        return $this->getCurrentPanel()->getTenantProfileUrl($parameters);
    }

    public function getTenantRegistrationPage(): ?string
    {
        return $this->getCurrentPanel()->getTenantRegistrationPage();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantRegistrationUrl(array $parameters = []): ?string
    {
        return $this->getCurrentPanel()->getTenantRegistrationUrl($parameters);
    }

    public function getTheme(): Theme
    {
        return $this->getCurrentPanel()->getTheme();
    }

    public function getUserAvatarUrl(Model | Authenticatable $user): string
    {
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
        $panel = $this->getCurrentPanel();

        if ($user instanceof HasDefaultTenant) {
            $tenant = $user->getDefaultTenant($panel);
        }

        if (! $tenant) {
            $tenant = Arr::first($this->getUserTenants($user));
        }

        return $tenant;
    }

    /**
     * @return array<Action>
     */
    public function getUserMenuItems(): array
    {
        return $this->getCurrentPanel()->getUserMenuItems();
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
        $tenants = $user->getTenants($this->getCurrentPanel());

        if ($tenants instanceof Collection) {
            $tenants = $tenants->all();
        }

        return $tenants;
    }

    public function getUrl(?Model $tenant = null): ?string
    {
        return $this->getCurrentPanel()->getUrl($tenant);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user, array $parameters = []): string
    {
        return $this->getCurrentPanel()->getVerifyEmailUrl($user, $parameters);
    }

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return $this->getCurrentPanel()->getWidgets();
    }

    public function getDatabaseNotificationsMarkAllAsReadAction(): Action
    {
        return $this->currentPanel->getDatabaseNotificationsMarkAllAsReadAction();
    }

    public function hasBreadcrumbs(): bool
    {
        return $this->getCurrentPanel()->hasBreadcrumbs();
    }

    public function hasBroadcasting(): bool
    {
        return $this->getCurrentPanel()->hasBroadcasting();
    }

    public function hasCollapsibleNavigationGroups(): bool
    {
        return $this->getCurrentPanel()->hasCollapsibleNavigationGroups();
    }

    public function hasDarkMode(): bool
    {
        return $this->getCurrentPanel()->hasDarkMode();
    }

    public function hasDarkModeForced(): bool
    {
        return $this->getCurrentPanel()->hasDarkModeForced();
    }

    public function hasDatabaseNotifications(): bool
    {
        return $this->getCurrentPanel()->hasDatabaseNotifications();
    }

    public function hasEmailVerification(): bool
    {
        return $this->getCurrentPanel()->hasEmailVerification();
    }

    public function hasLogin(): bool
    {
        return $this->getCurrentPanel()->hasLogin();
    }

    public function hasNavigation(): bool
    {
        return $this->getCurrentPanel()->hasNavigation();
    }

    public function hasPasswordReset(): bool
    {
        return $this->getCurrentPanel()->hasPasswordReset();
    }

    public function hasPlugin(string $id): bool
    {
        return $this->getCurrentPanel()->hasPlugin($id);
    }

    public function hasProfile(): bool
    {
        return $this->getCurrentPanel()->hasProfile();
    }

    public function hasRegistration(): bool
    {
        return $this->getCurrentPanel()->hasRegistration();
    }

    public function hasTenantMenu(): bool
    {
        return $this->getCurrentPanel()->hasTenantMenu();
    }

    public function hasTenancy(): bool
    {
        return $this->getCurrentPanel()->hasTenancy();
    }

    public function hasTenantBilling(): bool
    {
        return $this->getCurrentPanel()->hasTenantBilling();
    }

    public function hasTenantProfile(): bool
    {
        return $this->getCurrentPanel()->hasTenantProfile();
    }

    public function hasTenantRegistration(): bool
    {
        return $this->getCurrentPanel()->hasTenantRegistration();
    }

    public function hasTopbar(): bool
    {
        return $this->getCurrentPanel()->hasTopbar();
    }

    public function hasTopNavigation(): bool
    {
        return $this->getCurrentPanel()->hasTopNavigation();
    }

    public function hasUnsavedChangesAlerts(): bool
    {
        return $this->getCurrentPanel()->hasUnsavedChangesAlerts();
    }

    public function isGlobalSearchEnabled(): bool
    {
        if ($this->getGlobalSearchProvider() === null) {
            return false;
        }

        foreach ($this->getResources() as $resource) {
            if ($resource::canGloballySearch()) {
                return true;
            }
        }

        return false;
    }

    public function isServing(): bool
    {
        return $this->isServing;
    }

    public function isSidebarCollapsibleOnDesktop(): bool
    {
        return $this->getCurrentPanel()->isSidebarCollapsibleOnDesktop();
    }

    public function isSidebarFullyCollapsibleOnDesktop(): bool
    {
        return $this->getCurrentPanel()->isSidebarFullyCollapsibleOnDesktop();
    }

    public function registerPanel(Panel $panel): void
    {
        app(PanelRegistry::class)->register($panel);
    }

    /**
     * @deprecated Use the `\Filament\Support\Facades\FilamentView::renderHook()` method instead.
     */
    public function renderHook(string $name): Htmlable
    {
        return FilamentView::renderHook($name);
    }

    public function serving(Closure $callback): void
    {
        Event::listen(ServingFilament::class, $callback);
    }

    public function currentDomain(?string $domain): void
    {
        $this->currentDomain = $domain;
    }

    public function setCurrentPanel(?Panel $panel): void
    {
        $this->currentPanel = $panel;
    }

    public function setServingStatus(bool $condition = true): void
    {
        $this->isServing = $condition;
    }

    public function setTenant(?Model $tenant, bool $isQuiet = false): void
    {
        $this->tenant = $tenant;

        if ($tenant && (! $isQuiet)) {
            event(new TenantSet($tenant, $this->auth()->user()));
        }
    }

    /**
     * @deprecated Use the `navigationGroups()` method on the panel configuration instead.
     *
     * @param  array<string | int, NavigationGroup | string>  $groups
     */
    public function registerNavigationGroups(array $groups): void
    {
        try {
            $this->getDefaultPanel()->navigationGroups($groups);
        } catch (NoDefaultPanelSetException $exception) {
            throw new Exception('Please use the `navigationGroups()` method on the panel configuration to register navigation groups. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-navigation-groups');
        }
    }

    /**
     * @deprecated Use the `navigationItems()` method on the panel configuration instead.
     *
     * @param  array<NavigationItem>  $items
     */
    public function registerNavigationItems(array $items): void
    {
        try {
            $this->getDefaultPanel()->navigationItems($items);
        } catch (NoDefaultPanelSetException $exception) {
            throw new Exception('Please use the `navigationItems()` method on the panel configuration to register navigation items. See the documentation - https://filamentphp.com/docs/panels/navigation#registering-custom-navigation-items');
        }
    }

    /**
     * @deprecated Use the `pages()` method on the panel configuration instead.
     *
     * @param  array<class-string>  $pages
     */
    public function registerPages(array $pages): void
    {
        try {
            $this->getDefaultPanel()->pages($pages);
        } catch (NoDefaultPanelSetException $exception) {
            throw new Exception('Please use the `pages()` method on the panel configuration to register pages.');
        }
    }

    /**
     * @deprecated Use the `renderHook()` method on the panel configuration instead.
     */
    public function registerRenderHook(string $name, Closure $hook): void
    {
        FilamentView::registerRenderHook($name, $hook);
    }

    /**
     * @deprecated Use the `resources()` method on the panel configuration instead.
     *
     * @param  array<class-string>  $resources
     */
    public function registerResources(array $resources): void
    {
        try {
            $this->getDefaultPanel()->resources($resources);
        } catch (NoDefaultPanelSetException $exception) {
            throw new Exception('Please use the `resources()` method on the panel configuration to register resources.');
        }
    }

    /**
     * @deprecated Register scripts using the `FilamentAsset` facade instead.
     *
     * @param  array<mixed>  $scripts
     */
    public function registerScripts(array $scripts, bool $shouldBeLoadedBeforeCoreScripts = false): void
    {
        throw new Exception('Please use the `FilamentAsset` facade to register scripts. See the documentation - https://filamentphp.com/docs/support/assets#registering-javascript-files');
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
        throw new Exception('Please use the `FilamentAsset` facade to register styles. See the documentation - https://filamentphp.com/docs/support/assets#registering-css-files');
    }

    /**
     * @deprecated Use the `theme()` method on the panel configuration instead.
     */
    public function registerTheme(string | Htmlable | null $theme): void
    {
        try {
            $this->getDefaultPanel()->theme($theme);
        } catch (NoDefaultPanelSetException $exception) {
            throw new Exception('Please use the `theme()` method on the panel configuration to register themes.');
        }
    }

    /**
     * @deprecated Use the `viteTheme()` method on the panel configuration instead.
     *
     * @param  string | array<string>  $theme
     */
    public function registerViteTheme(string | array $theme, ?string $buildDirectory = null): void
    {
        try {
            $this->getDefaultPanel()->viteTheme($theme, $buildDirectory);
        } catch (NoDefaultPanelSetException $exception) {
            throw new Exception('Please use the `viteTheme()` method on the panel configuration to register themes.');
        }
    }

    /**
     * @deprecated Use the `userMenuItems()` method on the panel configuration instead.
     *
     * @param  array<MenuItem>  $items
     */
    public function registerUserMenuItems(array $items): void
    {
        try {
            $this->getDefaultPanel()->userMenuItems($items);
        } catch (NoDefaultPanelSetException $exception) {
            throw new Exception('Please use the `userMenuItems()` method on the panel configuration to register user menu items. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-the-user-menu');
        }
    }

    /**
     * @deprecated Use the `widgets()` method on the panel configuration instead.
     *
     * @param  array<class-string>  $widgets
     */
    public function registerWidgets(array $widgets): void
    {
        try {
            $this->getDefaultPanel()->widgets($widgets);
        } catch (NoDefaultPanelSetException $exception) {
            throw new Exception('Please use the `widgets()` method on the panel configuration to register widgets.');
        }
    }

    public function getDefaultThemeMode(): ThemeMode
    {
        return $this->getCurrentPanel()->getDefaultThemeMode();
    }

    public function arePasswordsRevealable(): bool
    {
        return $this->getCurrentPanel()->arePasswordsRevealable();
    }

    public function getCurrentDomain(?string $testingDomain = null): string
    {
        if (filled($this->currentDomain)) {
            return $this->currentDomain;
        }

        if (app()->runningUnitTests()) {
            return $testingDomain;
        }

        if (app()->runningInConsole()) {
            throw new Exception('The current domain is not set, but multiple domains are registered for the panel. Please use [Filament::currentDomain(\'example.com\')] to set the current domain to ensure that panel URLs are generated correctly.');
        }

        return request()->getHost();
    }
}
