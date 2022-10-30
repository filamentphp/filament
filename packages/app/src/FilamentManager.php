<?php

namespace Filament;

use Closure;
use Exception;
use Filament\Events\ServingFilament;
use Filament\Events\TenantSet;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Http\Middleware\MirrorConfigToSubpackages;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class FilamentManager
{
    protected ?Context $currentContext = null;

    protected array $contexts = [];

    protected ?Model $tenant = null;

    public function auth(): Guard
    {
        return $this->getCurrentContext()->auth();
    }

    public function bootCurrentContext(): void
    {
        $this->getCurrentContext()->boot();
    }

    public function navigation(Closure $builder, ?string $context = null): void
    {
        $this->getContext($context)->navigation($builder);
    }

    public function buildNavigation(): array
    {
        return $this->getCurrentContext()->buildNavigation();
    }

    public function globalSearchProvider(string $provider, ?string $context = null): void
    {
        $this->getContext($context)->globalSearchProvider($provider);
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

    public function registerNavigationGroups(array $groups, ?string $context = null): void
    {
        $this->getContext($context)->navigationGroups($groups);
    }

    public function registerNavigationItems(array $items, ?string $context = null): void
    {
        $this->getContext($context)->navigationItems($items);
    }

    public function registerPages(array $pages, ?string $context = null): void
    {
        $this->getContext($context)->pages($pages);
    }

    public function registerRenderHook(string $name, Closure $callback, ?string $context = null): void
    {
        $this->getContext($context)->renderHook($name, $callback);
    }

    public function registerResources(array $resources, ?string $context = null): void
    {
        $this->getContext($context)->resources($resources);
    }

    public function registerTheme(string | Htmlable | null $theme, ?string $context = null): void
    {
        $this->getContext($context)->theme($theme);
    }

    /**
     * @deprecated Use `registerTheme()` instead.
     */
    public function registerThemeUrl(string | Htmlable | null $theme): void
    {
        $this->registerTheme($theme);
    }

    /**
     * @deprecated Use `registerTheme()` instead.
     */
    public function registerThemeLink(string | Htmlable | null $theme): void
    {
        $this->registerTheme($theme);
    }

    public function registerTenantMenuItems(array $items, ?string $context = null): void
    {
        $this->getContext($context)->tenantMenuItems($items);
    }

    public function registerUserMenuItems(array $items, ?string $context = null): void
    {
        $this->getContext($context)->userMenuItems($items);
    }

    public function registerWidgets(array $widgets, ?string $context = null): void
    {
        $this->getContext($context)->widgets($widgets);
    }

    public function pushMeta(array $meta, ?string $context = null): void
    {
        $this->getContext($context)->meta($meta);
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

    public function hasLogin(): bool
    {
        return $this->getCurrentContext()->hasLogin();
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

    public function getLoginUrl(): ?string
    {
        return $this->getCurrentContext()->getLoginUrl();
    }

    public function getTenantBillingProvider(): ?Billing\Providers\Contracts\Provider
    {
        return $this->getCurrentContext()->getTenantBillingProvider();
    }

    public function getTenantBillingUrl(Model $tenant): ?string
    {
        return $this->getCurrentContext()->getTenantBillingUrl($tenant);
    }

    public function getTenantRegistrationPage(): ?string
    {
        return $this->getCurrentContext()->getTenantRegistrationPage();
    }

    public function getTenantRegistrationUrl(): ?string
    {
        return $this->getCurrentContext()->getTenantRegistrationUrl();
    }

    public function getLogoutUrl(): string
    {
        return $this->getCurrentContext()->getLogoutUrl();
    }

    public function getNavigation(): array
    {
        return $this->getCurrentContext()->getNavigation();
    }

    public function getNavigationGroups(): array
    {
        return $this->getCurrentContext()->getNavigationGroups();
    }

    public function getNavigationItems(): array
    {
        return $this->getCurrentContext()->getNavigationItems();
    }

    public function getPages(): array
    {
        return $this->getCurrentContext()->getPages();
    }

    public function getResources(): array
    {
        return $this->getCurrentContext()->getResources();
    }

    public function getTenantMenuItems(): array
    {
        return $this->getCurrentContext()->getTenantMenuItems();
    }

    public function getUserMenuItems(): array
    {
        return $this->getCurrentContext()->getUserMenuItems();
    }

    public function getModelResource(string | Model $model): ?string
    {
        return $this->getCurrentContext()->getModelResource($model);
    }

    public function getTheme(): string | Htmlable | null
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

        $provider = config('filament.default_avatar_provider');

        return app($provider)->get($tenant);
    }

    public function getTenantName(Model $tenant): string
    {
        if ($tenant instanceof HasName) {
            return $tenant->getFilamentName();
        }

        return $tenant->getAttributeValue('name');
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

        $provider = config('filament.default_avatar_provider');

        return app($provider)->get($user);
    }

    public function getUserName(Model | Authenticatable $user): string
    {
        if ($user instanceof HasName) {
            return $user->getFilamentName();
        }

        return $user->getAttributeValue('name');
    }

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

    public function getWidgets(): array
    {
        return $this->getCurrentContext()->getWidgets();
    }

    public function getMeta(): array
    {
        return $this->getCurrentContext()->getMeta();
    }
}
