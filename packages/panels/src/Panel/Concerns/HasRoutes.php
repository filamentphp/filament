<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasRoutes
{
    protected ?Closure $routes = null;

    protected ?Closure $authenticatedRoutes = null;

    protected ?Closure $tenantRoutes = null;

    protected ?Closure $authenticatedTenantRoutes = null;

    protected string | Closure | null $homeUrl = null;

    protected ?string $domain = null;

    protected string $path = '';

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function domain(?string $domain = null): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function homeUrl(string | Closure | null $url = null): static
    {
        $this->homeUrl = $url;

        return $this;
    }

    public function routes(?Closure $routes): static
    {
        $this->routes = $routes;

        return $this;
    }

    public function authenticatedRoutes(?Closure $routes): static
    {
        $this->authenticatedRoutes = $routes;

        return $this;
    }

    public function tenantRoutes(?Closure $routes): static
    {
        $this->tenantRoutes = $routes;

        return $this;
    }

    public function authenticatedTenantRoutes(?Closure $routes): static
    {
        $this->authenticatedTenantRoutes = $routes;

        return $this;
    }

    public function getRoutes(): ?Closure
    {
        return $this->routes;
    }

    public function getAuthenticatedRoutes(): ?Closure
    {
        return $this->authenticatedRoutes;
    }

    public function getTenantRoutes(): ?Closure
    {
        return $this->tenantRoutes;
    }

    public function getAuthenticatedTenantRoutes(): ?Closure
    {
        return $this->authenticatedTenantRoutes;
    }

    public function getHomeUrl(): ?string
    {
        return $this->evaluate($this->homeUrl);
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUrl(?Model $tenant = null): ?string
    {
        if (! $this->auth()->check()) {
            return $this->hasLogin() ? $this->getLoginUrl() : url($this->getPath());
        }

        $hasTenancy = $this->hasTenancy();

        if ((! $tenant) && $hasTenancy) {
            $tenant = Filament::getUserDefaultTenant($this->auth()->user());
        }

        if ((! $tenant) && $hasTenancy) {
            return $this->hasTenantRegistration() ? $this->getTenantRegistrationUrl() : null;
        }

        if ($tenant) {
            $originalTenant = Filament::getTenant();
            Filament::setTenant($tenant);

            $isNavigationMountedOriginally = $this->isNavigationMounted;
            $originalNavigationItems = $this->navigationItems;
            $originalNavigationGroups = $this->navigationGroups;

            $this->isNavigationMounted = false;
            $this->navigationItems = [];
            $this->navigationGroups = [];

            $navigation = $this->getNavigation();

            Filament::setTenant($originalTenant);

            $this->isNavigationMounted = $isNavigationMountedOriginally;
            $this->navigationItems = $originalNavigationItems;
            $this->navigationGroups = $originalNavigationGroups;
        } else {
            $navigation = $this->getNavigation();
        }

        $firstGroup = Arr::first($navigation);

        if (! $firstGroup) {
            return null;
        }

        $firstItem = Arr::first($firstGroup->getItems());

        if (! $firstItem) {
            return null;
        }

        return $firstItem->getUrl();
    }
}
