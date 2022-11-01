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
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

class FilamentManager
{
    protected ?Context $currentContext = null;

    protected array $contexts = [];

    protected ?Model $tenant = null;

    protected ?string $favicon = null;

    public function auth(): Guard
    {
        return $this->getCurrentContext()->auth();
    }

    public function bootCurrentContext(): void
    {
        $this->getCurrentContext()->boot();
    }

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

    public function favicon(?string $url): void
    {
        $this->favicon = $url;
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

    public function getEmailVerificationPromptUrl(): ?string
    {
        return $this->getCurrentContext()->getEmailVerificationPromptUrl();
    }

    public function getEmailVerifiedMiddleware(): string
    {
        return $this->getCurrentContext()->getEmailVerifiedMiddleware();
    }

    public function getLoginUrl(): ?string
    {
        return $this->getCurrentContext()->getLoginUrl();
    }

    public function getRegistrationUrl(): ?string
    {
        return $this->getCurrentContext()->getRegistrationUrl();
    }

    public function getRequestPasswordResetUrl(): ?string
    {
        return $this->getCurrentContext()->getRequestPasswordResetUrl();
    }

    public function getVerifyEmailUrl(MustVerifyEmail $user): string
    {
        return $this->getCurrentContext()->getVerifyEmailUrl($user);
    }

    public function getResetPasswordUrl(string $token, CanResetPassword $user): string
    {
        return $this->getCurrentContext()->getResetPasswordUrl($token, $user);
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

        return app($this->getDefaultAvatarProvider())->get($tenant);
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

        return app($this->getDefaultAvatarProvider())->get($user);
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

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }

    public function hasDarkMode(): bool
    {
        return $this->getCurrentContext()->hasDarkMode();
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

    public function getGoogleFonts(): ?string
    {
        return $this->getCurrentContext()->getGoogleFonts();
    }

    public function getDefaultAvatarProvider(): string
    {
        return $this->getCurrentContext()->getDefaultAvatarProvider();
    }

    public function getPlugin(string $id): Plugin
    {
        return $this->getCurrentContext()->getPlugin($id);
    }
}
