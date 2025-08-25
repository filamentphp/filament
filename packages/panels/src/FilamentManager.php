<?php

namespace Filament;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Contracts\Plugin;
use Filament\Enums\ThemeMode;
use Filament\Events\ServingFilament;
use Filament\Events\TenantSet;
use Filament\Exceptions\NoDefaultPanelSetException;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Assets\Theme;
use Filament\Support\Enums\Width;
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
use Livewire\Component;

class FilamentManager
{
    protected ?string $currentDomain = null;

    protected ?Panel $currentPanel = null;

    protected bool $isServing = false;

    protected bool $isCurrentPanelBooted = false;

    protected ?Model $tenant = null;

    public function auth(): Guard
    {
        return $this->getCurrentOrDefaultPanel()->auth();
    }

    public function bootCurrentPanel(): void
    {
        if ($this->isCurrentPanelBooted) {
            return;
        }

        $this->getCurrentOrDefaultPanel()->boot();

        $this->isCurrentPanelBooted = true;
    }

    /**
     * @return array<NavigationGroup>
     */
    public function buildNavigation(): array
    {
        return $this->getCurrentOrDefaultPanel()->buildNavigation();
    }

    public function getAuthGuard(): string
    {
        return $this->getCurrentOrDefaultPanel()->getAuthGuard();
    }

    public function getAuthPasswordBroker(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getAuthPasswordBroker();
    }

    public function getBrandName(): string | Htmlable
    {
        return $this->getCurrentOrDefaultPanel()->getBrandName();
    }

    public function getBrandLogo(): string | Htmlable | null
    {
        return $this->getCurrentOrDefaultPanel()->getBrandLogo();
    }

    public function getBrandLogoHeight(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getBrandLogoHeight();
    }

    public function getCollapsedSidebarWidth(): string
    {
        return $this->getCurrentOrDefaultPanel()->getCollapsedSidebarWidth();
    }

    /**
     * @throws NoDefaultPanelSetException
     */
    public function getCurrentOrDefaultPanel(): ?Panel
    {
        return $this->getCurrentPanel() ?? $this->getDefaultPanel();
    }

    public function getCurrentPanel(): ?Panel
    {
        return $this->currentPanel;
    }

    public function getDarkModeBrandLogo(): string | Htmlable | null
    {
        return $this->getCurrentOrDefaultPanel()->getDarkModeBrandLogo();
    }

    public function getDatabaseNotificationsPollingInterval(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getDatabaseNotificationsPollingInterval();
    }

    public function getDefaultAvatarProvider(): string
    {
        return $this->getCurrentOrDefaultPanel()->getDefaultAvatarProvider();
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
        return $this->getCurrentOrDefaultPanel()->getEmailVerificationPromptUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getSetUpRequiredMultiFactorAuthenticationUrl(array $parameters = []): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getSetUpRequiredMultiFactorAuthenticationUrl($parameters);
    }

    public function getEmailVerifiedMiddleware(): string
    {
        return $this->getCurrentOrDefaultPanel()->getEmailVerifiedMiddleware();
    }

    public function getFavicon(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getFavicon();
    }

    public function getFontFamily(): string
    {
        return $this->getCurrentOrDefaultPanel()->getFontFamily();
    }

    public function getMonoFontFamily(): string
    {
        return $this->getCurrentOrDefaultPanel()->getMonoFontFamily();
    }

    public function getSerifFontFamily(): string
    {
        return $this->getCurrentOrDefaultPanel()->getSerifFontFamily();
    }

    public function getFontHtml(): Htmlable
    {
        return $this->getCurrentOrDefaultPanel()->getFontHtml();
    }

    public function getMonoFontHtml(): Htmlable
    {
        return $this->getCurrentOrDefaultPanel()->getMonoFontHtml();
    }

    public function getSerifFontHtml(): Htmlable
    {
        return $this->getCurrentOrDefaultPanel()->getSerifFontHtml();
    }

    public function getFontProvider(): string
    {
        return $this->getCurrentOrDefaultPanel()->getFontProvider();
    }

    public function getMonoFontProvider(): string
    {
        return $this->getCurrentOrDefaultPanel()->getMonoFontProvider();
    }

    public function getSerifFontProvider(): string
    {
        return $this->getCurrentOrDefaultPanel()->getSerifFontProvider();
    }

    public function getFontUrl(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getFontUrl();
    }

    public function getMonoFontUrl(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getMonoFontUrl();
    }

    public function getSerifFontUrl(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getSerifFontUrl();
    }

    public function getGlobalSearchDebounce(): string
    {
        return $this->getCurrentOrDefaultPanel()->getGlobalSearchDebounce();
    }

    /**
     * @return array<string>
     */
    public function getGlobalSearchKeyBindings(): array
    {
        return $this->getCurrentOrDefaultPanel()->getGlobalSearchKeyBindings();
    }

    public function getGlobalSearchFieldSuffix(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getGlobalSearchFieldSuffix();
    }

    public function getGlobalSearchProvider(): ?GlobalSearchProvider
    {
        return $this->getCurrentOrDefaultPanel()->getGlobalSearchProvider();
    }

    public function getHomeUrl(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getHomeUrl() ?? $this->getCurrentOrDefaultPanel()->getUrl();
    }

    public function getId(): ?string
    {
        return $this->getCurrentOrDefaultPanel()?->getId();
    }

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return $this->getCurrentOrDefaultPanel()?->getSubNavigationPosition();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getLoginUrl(array $parameters = []): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getLoginUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getLogoutUrl(array $parameters = []): string
    {
        return $this->getCurrentOrDefaultPanel()->getLogoutUrl($parameters);
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return $this->getCurrentOrDefaultPanel()->getMaxContentWidth();
    }

    public function getSimplePageMaxContentWidth(): Width | string | null
    {
        return $this->getCurrentOrDefaultPanel()->getSimplePageMaxContentWidth();
    }

    /**
     * @param  class-string<Model>|Model  $model
     */
    public function getModelResource(string | Model $model): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getModelResource($model);
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
        return $this->getCurrentOrDefaultPanel()->getNavigation();
    }

    /**
     * @return array<string | int, NavigationGroup | string>
     */
    public function getNavigationGroups(): array
    {
        return $this->getCurrentOrDefaultPanel()->getNavigationGroups();
    }

    /**
     * @return array<NavigationItem>
     */
    public function getNavigationItems(): array
    {
        return $this->getCurrentOrDefaultPanel()->getNavigationItems();
    }

    /**
     * @return array<string | int, array<class-string> | class-string>
     */
    public function getClusteredComponents(?string $cluster): array
    {
        return $this->getCurrentOrDefaultPanel()->getClusteredComponents($cluster);
    }

    /**
     * @return array<class-string>
     */
    public function getPages(): array
    {
        return $this->getCurrentOrDefaultPanel()->getPages();
    }

    public function getPanel(?string $id = null, bool $isStrict = true): ?Panel
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
        return $this->getCurrentOrDefaultPanel()->getPlugin($id);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getProfileUrl(array $parameters = []): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getProfileUrl($parameters);
    }

    public function isProfilePageSimple(): bool
    {
        return $this->getCurrentOrDefaultPanel()->isProfilePageSimple();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getRegistrationUrl(array $parameters = []): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getRegistrationUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getRequestPasswordResetUrl(array $parameters = []): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getRequestPasswordResetUrl($parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user, array $parameters = []): string
    {
        return $this->getCurrentOrDefaultPanel()->getResetPasswordUrl($token, $user, $parameters);
    }

    /**
     * @return array<class-string>
     */
    public function getResources(): array
    {
        return $this->getCurrentOrDefaultPanel()->getResources();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getResourceUrl(string | Model $model, string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?Model $tenant = null): string
    {
        return $this->getCurrentOrDefaultPanel()->getResourceUrl($model, $name, $parameters, $isAbsolute, $tenant);
    }

    public function getSidebarWidth(): string
    {
        return $this->getCurrentOrDefaultPanel()->getSidebarWidth();
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

    public function getTenantBillingProvider(): ?Billing\Providers\Contracts\BillingProvider
    {
        return $this->getCurrentOrDefaultPanel()->getTenantBillingProvider();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantBillingUrl(array $parameters = [], ?Model $tenant = null): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getTenantBillingUrl($tenant ?? $this->getTenant(), $parameters);
    }

    /**
     * @return array<Action>
     */
    public function getTenantMenuItems(): array
    {
        return $this->getCurrentOrDefaultPanel()->getTenantMenuItems();
    }

    /**
     * @return class-string<Model>|null
     */
    public function getTenantModel(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getTenantModel();
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
        return $this->getCurrentOrDefaultPanel()->getTenantOwnershipRelationshipName();
    }

    public function getProfilePage(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getProfilePage();
    }

    /**
     * @return class-string<Component>
     */
    public function getSidebarLivewireComponent(): string
    {
        return $this->getCurrentOrDefaultPanel()->getSidebarLivewireComponent();
    }

    /**
     * @return class-string<Component>
     */
    public function getTopbarLivewireComponent(): string
    {
        return $this->getCurrentOrDefaultPanel()->getTopbarLivewireComponent();
    }

    public function getTenantProfilePage(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getTenantProfilePage();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantProfileUrl(array $parameters = []): ?string
    {
        $parameters['tenant'] ??= $this->getTenant();

        return $this->getCurrentOrDefaultPanel()->getTenantProfileUrl($parameters);
    }

    public function getTenantRegistrationPage(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getTenantRegistrationPage();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getTenantRegistrationUrl(array $parameters = []): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getTenantRegistrationUrl($parameters);
    }

    public function getTheme(): Theme
    {
        return $this->getCurrentOrDefaultPanel()->getTheme();
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
        $panel = $this->getCurrentOrDefaultPanel();

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
        return $this->getCurrentOrDefaultPanel()->getUserMenuItems();
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
        $tenants = $user->getTenants($this->getCurrentOrDefaultPanel());

        if ($tenants instanceof Collection) {
            $tenants = $tenants->all();
        }

        return $tenants;
    }

    public function getUrl(?Model $tenant = null): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getUrl($tenant);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user, array $parameters = []): string
    {
        return $this->getCurrentOrDefaultPanel()->getVerifyEmailUrl($user, $parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getVerifyEmailChangeUrl(MustVerifyEmail | Model | Authenticatable $user, string $newEmail, array $parameters = []): string
    {
        return $this->getCurrentOrDefaultPanel()->getVerifyEmailChangeUrl($user, $newEmail, $parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getBlockEmailChangeVerificationUrl(MustVerifyEmail | Model | Authenticatable $user, string $newEmail, string $verificationSignature, array $parameters = []): string
    {
        return $this->getCurrentOrDefaultPanel()->getBlockEmailChangeVerificationUrl($user, $newEmail, $verificationSignature, $parameters);
    }

    /**
     * @return array<class-string<Widget>>
     */
    public function getWidgets(): array
    {
        return $this->getCurrentOrDefaultPanel()->getWidgets();
    }

    public function hasBreadcrumbs(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasBreadcrumbs();
    }

    public function hasBroadcasting(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasBroadcasting();
    }

    public function hasCollapsibleNavigationGroups(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasCollapsibleNavigationGroups();
    }

    public function hasDarkMode(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasDarkMode();
    }

    public function hasDarkModeForced(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasDarkModeForced();
    }

    public function hasDatabaseNotifications(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasDatabaseNotifications();
    }

    public function hasErrorNotifications(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasErrorNotifications();
    }

    public function hasLazyLoadedDatabaseNotifications(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasLazyLoadedDatabaseNotifications();
    }

    public function hasMultiFactorAuthentication(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasMultiFactorAuthentication();
    }

    public function hasEmailVerification(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasEmailVerification();
    }

    public function hasEmailChangeVerification(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasEmailChangeVerification();
    }

    public function hasLogin(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasLogin();
    }

    public function hasNavigation(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasNavigation();
    }

    public function hasPasswordReset(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasPasswordReset();
    }

    public function hasPlugin(string $id): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasPlugin($id);
    }

    public function hasProfile(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasProfile();
    }

    public function hasRegistration(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasRegistration();
    }

    public function hasTenantMenu(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasTenantMenu();
    }

    public function hasTenancy(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasTenancy();
    }

    public function hasTenantBilling(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasTenantBilling();
    }

    public function hasTenantProfile(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasTenantProfile();
    }

    public function hasTenantRegistration(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasTenantRegistration();
    }

    public function hasTopbar(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasTopbar();
    }

    public function hasUserMenu(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasUserMenu();
    }

    public function hasTopNavigation(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasTopNavigation();
    }

    public function hasUnsavedChangesAlerts(): bool
    {
        return $this->getCurrentOrDefaultPanel()->hasUnsavedChangesAlerts();
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
        return $this->getCurrentOrDefaultPanel()->isSidebarCollapsibleOnDesktop();
    }

    public function isSidebarFullyCollapsibleOnDesktop(): bool
    {
        return $this->getCurrentOrDefaultPanel()->isSidebarFullyCollapsibleOnDesktop();
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

    public function setCurrentPanel(Panel | string | null $panel): void
    {
        if (is_string($panel)) {
            $panel = $this->getPanel($panel);
        }

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
     * @param  array<string | int, NavigationGroup | string>  $groups
     *
     * @deprecated Use the `navigationGroups()` method on the panel configuration instead.
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
     * @param  array<NavigationItem>  $items
     *
     * @deprecated Use the `navigationItems()` method on the panel configuration instead.
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
     * @param  array<class-string>  $pages
     *
     * @deprecated Use the `pages()` method on the panel configuration instead.
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
     * @param  array<class-string>  $resources
     *
     * @deprecated Use the `resources()` method on the panel configuration instead.
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
     * @param  array<mixed>  $scripts
     *
     * @deprecated Register scripts using the `FilamentAsset` facade instead.
     */
    public function registerScripts(array $scripts, bool $shouldBeLoadedBeforeCoreScripts = false): void
    {
        throw new Exception('Please use the `FilamentAsset` facade to register scripts. See the documentation - https://filamentphp.com/docs/support/assets#registering-javascript-files');
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @deprecated Register script data using the `FilamentAsset` facade instead.
     */
    public function registerScriptData(array $data): void
    {
        FilamentAsset::registerScriptData($data);
    }

    /**
     * @param  array<mixed>  $styles
     *
     * @deprecated Register styles using the `FilamentAsset` facade instead.
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
     * @param  string | array<string>  $theme
     *
     * @deprecated Use the `viteTheme()` method on the panel configuration instead.
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
     * @param  array<MenuItem>  $items
     *
     * @deprecated Use the `userMenuItems()` method on the panel configuration instead.
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
     * @param  array<class-string>  $widgets
     *
     * @deprecated Use the `widgets()` method on the panel configuration instead.
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
        return $this->getCurrentOrDefaultPanel()->getDefaultThemeMode();
    }

    public function arePasswordsRevealable(): bool
    {
        return $this->getCurrentOrDefaultPanel()->arePasswordsRevealable();
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

    public function getTenancyScopeName(): string
    {
        return $this->getCurrentOrDefaultPanel()->getTenancyScopeName();
    }

    /**
     * @return array<string, MultiFactorAuthenticationProvider>
     */
    public function getMultiFactorAuthenticationProviders(): array
    {
        return $this->getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders();
    }

    public function isAuthorizationStrict(): bool
    {
        return $this->getCurrentOrDefaultPanel()->isAuthorizationStrict();
    }

    public function getResourceCreatePageRedirect(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getResourceCreatePageRedirect();
    }

    public function getResourceEditPageRedirect(): ?string
    {
        return $this->getCurrentOrDefaultPanel()->getResourceEditPageRedirect();
    }

    /**
     * @return array<array{ title: string | Closure, body: string | Closure | null }>
     */
    public function getErrorNotifications(): array
    {
        return $this->getCurrentOrDefaultPanel()->getErrorNotifications();
    }
}
