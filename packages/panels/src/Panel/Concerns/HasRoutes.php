<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Laravel\SerializableClosure\Serializers\Native;

trait HasRoutes
{
    protected Closure | Native | null $routes = null;

    protected Closure | Native | null $authenticatedRoutes = null;

    protected Closure | Native | null $tenantRoutes = null;

    protected Closure | Native | null $authenticatedTenantRoutes = null;

    protected string | Closure | null $homeUrl = null;

    /**
     * @var array<string>
     */
    protected array $domains = [];

    protected string $path = '';

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function domain(?string $domain): static
    {
        $this->domains(filled($domain) ? [$domain] : []);

        return $this;
    }

    /**
     * @param  array<string>  $domains
     */
    public function domains(array $domains): static
    {
        $this->domains = $domains;

        return $this;
    }

    public function homeUrl(string | Closure | null $url): static
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

    public function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return route($this->generateRouteName($name), $parameters, $absolute);
    }

    public function generateRouteName(string $name): string
    {
        return "filament.{$this->getId()}.{$name}";
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

    /**
     * @return array<string>
     */
    public function getDomains(): array
    {
        return Arr::wrap($this->domains);
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
            return ($this->hasTenantRegistration() && filament()->getTenantRegistrationPage()::canView()) ?
                $this->getTenantRegistrationUrl() :
                null;
        }

        if ($tenant) {
            $originalTenant = Filament::getTenant();
            Filament::setTenant($tenant, isQuiet: true);

            $isNavigationMountedOriginally = $this->isNavigationMounted;
            $originalNavigationItems = $this->navigationItems;
            $originalNavigationGroups = $this->navigationGroups;

            $this->isNavigationMounted = false;
            $this->navigationItems = [];
            $this->navigationGroups = [];

            $navigation = $this->getNavigation();
        }

        $navigation = $this->getNavigation();

        try {
            $firstGroup = Arr::first($navigation);

            if (! $firstGroup) {
                return url($this->getPath());
            }

            $firstItem = Arr::first($firstGroup->getItems());

            if (! $firstItem) {
                return url($this->getPath());
            }

            return $firstItem->getUrl();
        } finally {
            if ($tenant) {
                Filament::setTenant($originalTenant, isQuiet: true);

                $this->isNavigationMounted = $isNavigationMountedOriginally;
                $this->navigationItems = $originalNavigationItems;
                $this->navigationGroups = $originalNavigationGroups;
            }
        }
    }
}
