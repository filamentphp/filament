<?php

namespace Filament\Pages\Concerns;

use Filament\Panel;
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

    public static function registerRoutes(Panel $panel): void
    {
        static::routes($panel);
    }

    public static function routes(Panel $panel): void
    {
        Route::get(static::getRoutePath(), static::class)
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->name(static::getRelativeRouteName());
    }

    public static function getRoutePath(): string
    {
        return '/' . static::getSlug();
    }

    public static function getRelativeRouteName(): string
    {
        return (string) str(static::getSlug())->replace('/', '.');
    }

    public static function getSlug(): string
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
    public static function getRouteMiddleware(Panel $panel): string | array
    {
        return [
            ...(static::isEmailVerificationRequired($panel) ? [static::getEmailVerifiedMiddleware($panel)] : []),
            ...(static::isTenantSubscriptionRequired($panel) ? [static::getTenantSubscribedMiddleware($panel)] : []),
            ...Arr::wrap(static::$routeMiddleware),
        ];
    }

    /**
     * @return string | array<string>
     */
    public static function getWithoutRouteMiddleware(Panel $panel): string | array
    {
        return static::$withoutRouteMiddleware;
    }

    public static function getEmailVerifiedMiddleware(Panel $panel): string
    {
        return $panel->getEmailVerifiedMiddleware();
    }

    public static function isEmailVerificationRequired(Panel $panel): bool
    {
        return $panel->isEmailVerificationRequired();
    }

    public static function getTenantSubscribedMiddleware(Panel $panel): string
    {
        return $panel->getTenantBillingProvider()->getSubscribedMiddleware();
    }

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return $panel->isTenantSubscriptionRequired();
    }
}
