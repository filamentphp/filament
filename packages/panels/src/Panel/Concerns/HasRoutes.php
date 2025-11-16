<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Auth\Http\Controllers\BlockEmailChangeVerificationController;
use Filament\Auth\Http\Controllers\EmailChangeVerificationController;
use Filament\Auth\Http\Controllers\EmailVerificationController;
use Filament\Auth\Http\Controllers\LogoutController;
use Filament\Facades\Filament;
use Filament\Http\Controllers\RedirectToHomeController;
use Filament\Http\Controllers\RedirectToTenantController;
use Filament\Navigation\NavigationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\SerializableClosure\Serializers\Native;

trait HasRoutes
{
    /**
     * @var array<Closure | Native>
     */
    protected array $routes = [];

    /**
     * @var array<Closure | Native>
     */
    protected array $authenticatedRoutes = [];

    /**
     * @var array<Closure | Native>
     */
    protected array $tenantRoutes = [];

    /**
     * @var array<Closure | Native>
     */
    protected array $authenticatedTenantRoutes = [];

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
        $this->routes[] = $routes;

        return $this;
    }

    public function authenticatedRoutes(?Closure $routes): static
    {
        $this->authenticatedRoutes[] = $routes;

        return $this;
    }

    public function tenantRoutes(?Closure $routes): static
    {
        $this->tenantRoutes[] = $routes;

        return $this;
    }

    public function authenticatedTenantRoutes(?Closure $routes): static
    {
        $this->authenticatedTenantRoutes[] = $routes;

        return $this;
    }

    public function route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return route($this->generateRouteName($name), $parameters, $absolute);
    }

    public function generateRouteName(string $name): string
    {
        $domain = '';

        if (count($this->domains) > 1) {
            $domain = Filament::getCurrentDomain(testingDomain: Arr::first($this->domains)) . '.';
        }

        return "filament.{$this->getId()}.{$domain}{$name}";
    }

    /**
     * @return array<Closure | Native>
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return array<Closure | Native>
     */
    public function getAuthenticatedRoutes(): array
    {
        return $this->authenticatedRoutes;
    }

    /**
     * @return array<Closure | Native>
     */
    public function getTenantRoutes(): array
    {
        return $this->tenantRoutes;
    }

    /**
     * @return array<Closure | Native>
     */
    public function getAuthenticatedTenantRoutes(): array
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
        $hasTenancy = $this->hasTenancy();

        if ((! $tenant) && $hasTenancy && $this->auth()->hasUser()) {
            $tenant = Filament::getUserDefaultTenant($this->auth()->user());
        }

        if ($tenant && $this->hasTenantDomain()) {
            return $this->getRedirectUrl($tenant);
        }

        if (((! $hasTenancy) || $tenant) && Route::has($homeRouteName = $this->generateRouteName('home'))) {
            return route($homeRouteName, $tenant ? ['tenant' => $tenant] : []);
        }

        if ($tenant) {
            $tenantSlugAttribute = $this->getTenantSlugAttribute();
            $tenantRoutePrefix = $this->getTenantRoutePrefix() ?? '';

            if (filled($tenantRoutePrefix)) {
                $tenantRoutePrefix .= '/';
            }

            return url(Str::replaceEnd('/', '', $this->getPath()) . '/' . $tenantRoutePrefix . (filled($tenantSlugAttribute) ? $tenant->getAttributeValue($tenantSlugAttribute) : $tenant->getRouteKey()));
        }

        return url($this->getPath());
    }

    public function getRedirectUrl(?Model $tenant = null): ?string
    {
        if ((! $this->auth()->check()) && $this->hasLogin()) {
            return $this->getLoginUrl();
        }

        $hasTenancy = $this->hasTenancy();

        if ((! $tenant) && $hasTenancy && $this->auth()->hasUser()) {
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
        }

        $this->navigationManager = new NavigationManager;

        $navigation = $this->navigationManager->get();

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
            }

            $this->navigationManager = null;
        }
    }

    protected function registerRoutes(): void
    {

        Route::name('filament.')->group(function (): void {
            $thisId = $this->getId();
            $hasTenancy = $this->hasTenancy();
            $tenantRoutePrefix = $this->getTenantRoutePrefix();
            $tenantDomain = $this->getTenantDomain();
            $tenantSlugAttribute = $this->getTenantSlugAttribute();
            $domains = $this->getDomains();

            foreach ((empty($domains) ? [null] : $domains) as $domain) {
                Route::domain($domain)
                    ->middleware($this->getMiddleware())
                    ->name("{$thisId}." . ((filled($domain) && (count($domains) > 1)) ? "{$domain}." : ''))
                    ->prefix($this->getPath())
                    ->group(function () use ($hasTenancy, $tenantDomain, $tenantRoutePrefix, $tenantSlugAttribute): void {
                        foreach ($this->getRoutes() as $routes) {
                            $routes($this);
                        }

                        Route::name('auth.')->group(function (): void {
                            if ($this->hasLogin()) {
                                Route::get($this->getLoginRouteSlug(), $this->getLoginRouteAction())
                                    ->name('login');
                            }

                            if ($this->hasPasswordReset()) {
                                Route::name('password-reset.')
                                    ->prefix($this->getResetPasswordRoutePrefix())
                                    ->group(function (): void {
                                        Route::get($this->getRequestPasswordResetRouteSlug(), $this->getRequestPasswordResetRouteAction())
                                            ->name('request');
                                        Route::get($this->getResetPasswordRouteSlug(), $this->getResetPasswordRouteAction())
                                            ->middleware(['signed'])
                                            ->name('reset');
                                    });
                            }

                            if ($this->hasRegistration()) {
                                Route::get($this->getRegistrationRouteSlug(), $this->getRegistrationRouteAction())
                                    ->name('register');
                            }
                        });

                        Route::middleware($this->getAuthMiddleware())
                            ->group(function () use ($hasTenancy, $tenantDomain, $tenantRoutePrefix, $tenantSlugAttribute): void {
                                foreach ($this->getAuthenticatedRoutes() as $routes) {
                                    $routes($this);
                                }

                                Route::name('auth.')
                                    ->group(function (): void {
                                        Route::post('/logout', LogoutController::class)->name('logout');

                                        if ($this->hasProfile()) {
                                            $this->getProfilePage()::registerRoutes($this);
                                        }
                                    });

                                if ($this->hasEmailVerification()) {
                                    Route::name('auth.email-verification.')
                                        ->prefix($this->getEmailVerificationRoutePrefix())
                                        ->group(function (): void {
                                            Route::get($this->getEmailVerificationPromptRouteSlug(), $this->getEmailVerificationPromptRouteAction())
                                                ->name('prompt');
                                            Route::get($this->getEmailVerificationRouteSlug('/{id}/{hash}'), EmailVerificationController::class)
                                                ->middleware(['signed', 'throttle:6,1'])
                                                ->name('verify');
                                        });
                                }

                                if ($this->hasEmailChangeVerification()) {
                                    Route::name('auth.email-change-verification.')
                                        ->prefix($this->getEmailChangeVerificationRoutePrefix())
                                        ->group(function (): void {
                                            Route::get($this->getEmailChangeVerificationRouteSlug('/{id}/{email}'), EmailChangeVerificationController::class)
                                                ->middleware(['signed', 'throttle:6,1'])
                                                ->name('verify');

                                            Route::get($this->getEmailChangeVerificationRouteSlug('/{id}/{email}/block'), BlockEmailChangeVerificationController::class)
                                                ->middleware(['signed', 'throttle:6,1'])
                                                ->name('block-verification');
                                        });
                                }

                                if ($this->hasMultiFactorAuthentication()) {
                                    Route::name('auth.multi-factor-authentication.')
                                        ->prefix($this->getMultiFactorAuthenticationRoutePrefix())
                                        ->group(function (): void {
                                            if ($this->isMultiFactorAuthenticationRequired()) {
                                                Route::get($this->getSetUpRequiredMultiFactorAuthenticationRouteSlug(), $this->getSetUpRequiredMultiFactorAuthenticationRouteAction())
                                                    ->name('set-up-required');
                                            }
                                        });
                                }

                                Route::name('tenant.')
                                    ->group(function (): void {
                                        if ($this->hasTenantRegistration()) {
                                            $this->getTenantRegistrationPage()::registerRoutes($this);
                                        }
                                    });

                                $routeGroup = Route::middleware($hasTenancy ? $this->getTenantMiddleware() : []);

                                if (filled($tenantDomain)) {
                                    $routeGroup->domain($tenantDomain);
                                } else {
                                    $routeGroup->prefix(
                                        ($hasTenancy && blank($tenantDomain)) ?
                                            (
                                                filled($tenantRoutePrefix) ?
                                                "{$tenantRoutePrefix}/" :
                                                ''
                                            ) . ('{tenant' . (
                                                filled($tenantSlugAttribute) ?
                                                    ":{$tenantSlugAttribute}" :
                                                    ''
                                            ) . '}') :
                                            '',
                                    );
                                }

                                $routeGroup
                                    ->group(function (): void {
                                        foreach ($this->getAuthenticatedTenantRoutes() as $routes) {
                                            $routes($this);
                                        }

                                        Route::get('/', RedirectToHomeController::class)->name('home');

                                        Route::name('tenant.')->group(function (): void {
                                            if ($this->hasTenantBilling()) {
                                                Route::get($this->getTenantBillingRouteSlug(), $this->getTenantBillingProvider()->getRouteAction())
                                                    ->name('billing');
                                            }

                                            if ($this->hasTenantProfile()) {
                                                $this->getTenantProfilePage()::registerRoutes($this);
                                            }
                                        });

                                        foreach ($this->getPages() as $page) {
                                            $page::registerRoutes($this);
                                        }

                                        foreach ($this->getResources() as $resource) {
                                            $resource::registerRoutes($this);
                                        }
                                    });

                                if ($hasTenancy) {
                                    Route::get('/', RedirectToTenantController::class)->name('tenant');
                                }
                            });

                        if ($hasTenancy) {
                            $routeGroup = Route::middleware($this->getTenantMiddleware());

                            if (filled($tenantDomain)) {
                                $routeGroup->domain($tenantDomain);
                            } else {
                                $routeGroup->prefix(
                                    (
                                        filled($tenantRoutePrefix) ?
                                        "{$tenantRoutePrefix}/" :
                                        ''
                                    ) . '{tenant' . (
                                        filled($tenantSlugAttribute) ?
                                        ":{$tenantSlugAttribute}" :
                                        ''
                                    ) . '}',
                                );
                            }

                            $routeGroup
                                ->group(function (): void {
                                    foreach ($this->getTenantRoutes() as $routes) {
                                        $routes($this);
                                    }
                                });
                        }
                    });
            }
        });
    }
}
