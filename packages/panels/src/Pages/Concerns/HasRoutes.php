<?php

namespace Filament\Pages\Concerns;

use Filament\Pages\PageConfiguration;
use Filament\Panel;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

trait HasRoutes
{
    protected static ?string $slug = null;

    /**
     * @var string | array<string>
     */
    protected static string | array $routeMiddleware = [];

    /**
     * @var string | array<string>
     */
    protected static string | array $withoutRouteMiddleware = [];

    #[Unexposed]
    public static function registerRoutes(Panel $panel, ?PageConfiguration $configuration = null): void
    {
        static::routes($panel, $configuration);
    }

    #[Unexposed]
    public static function routes(Panel $panel, ?PageConfiguration $configuration = null): void
    {
        $middleware = static::getRouteMiddleware($panel);
        $routePath = static::getRoutePath($panel);

        if ($configuration) {
            $middleware = [
                ...$middleware,
                "page-configuration:{$configuration->getKey()}",
            ];
        }

        $route = Route::get($routePath, static::class)
            ->middleware($middleware)
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name(static::getRelativeRouteName($panel));

        if ($panel->hasTenancy() && blank($panel->getTenantDomain()) && ($routePath === '/')) {
            $route->fallback();
        }
    }

    #[Unexposed]
    public static function getRoutePath(Panel $panel): string
    {
        return '/' . static::getSlug($panel);
    }

    #[Unexposed]
    public static function getRelativeRouteName(Panel $panel): string
    {
        return (string) str(static::getSlug($panel))->replace('/', '.');
    }

    #[Unexposed]
    public static function getSlug(?Panel $panel = null): string
    {
        if ($configuration = static::getConfiguration($panel)) {
            if (filled($configSlug = $configuration->getSlug())) {
                return $configSlug;
            }

            return static::getDefaultSlug() . '/' . $configuration->getKey();
        }

        return static::getDefaultSlug();
    }

    #[Unexposed]
    public static function getDefaultSlug(): string
    {
        if (filled(static::$slug)) {
            return static::$slug;
        }

        return (string) str(class_basename(static::class))
            ->kebab()
            ->slug();
    }

    /**
     * @return string | array<string>
     */
    #[Unexposed]
    public static function getRouteMiddleware(Panel $panel): string | array
    {
        return [
            ...(static::isEmailVerificationRequired($panel) ? [static::getEmailVerifiedMiddleware($panel)] : []),
            ...(static::isMultiFactorAuthenticationRequired($panel) ? [static::getMultiFactorAuthenticationRequiredMiddleware($panel)] : []),
            ...(static::isTenantSubscriptionRequired($panel) ? [static::getTenantSubscribedMiddleware($panel)] : []),
            ...Arr::wrap(static::$routeMiddleware),
        ];
    }

    /**
     * @return string | array<string>
     */
    #[Unexposed]
    public static function getWithoutRouteMiddleware(Panel $panel): string | array
    {
        return static::$withoutRouteMiddleware;
    }

    #[Unexposed]
    public static function getEmailVerifiedMiddleware(Panel $panel): string
    {
        return $panel->getEmailVerifiedMiddleware();
    }

    #[Unexposed]
    public static function getMultiFactorAuthenticationRequiredMiddleware(Panel $panel): string
    {
        return $panel->getMultiFactorAuthenticationRequiredMiddleware();
    }

    #[Unexposed]
    public static function isEmailVerificationRequired(Panel $panel): bool
    {
        return $panel->isEmailVerificationRequired();
    }

    #[Unexposed]
    public static function isMultiFactorAuthenticationRequired(Panel $panel): bool
    {
        return $panel->isMultiFactorAuthenticationRequired();
    }

    #[Unexposed]
    public static function getTenantSubscribedMiddleware(Panel $panel): string
    {
        return $panel->getTenantBillingProvider()->getSubscribedMiddleware();
    }

    #[Unexposed]
    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return $panel->isTenantSubscriptionRequired();
    }
}
