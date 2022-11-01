<?php

namespace Filament\Pages\Concerns;

use Filament\Context;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

trait HasRoutes
{
    protected static ?string $slug = null;

    protected static string | array $routeMiddleware = [];

    public static function routes(Context $context): void
    {
        $slug = static::getSlug();

        Route::get("/{$slug}", static::class)
            ->middleware(static::getRouteMiddleware($context))
            ->name($slug);
    }

    public static function getSlug(): string
    {
        return static::$slug ?? str(static::$title ?? class_basename(static::class))
            ->kebab()
            ->slug();
    }

    public static function getRouteMiddleware(Context $context): string | array
    {
        return array_merge(
            (static::isEmailVerificationRequired($context) ? [static::getEmailVerifiedMiddleware($context)] : []),
            (static::isTenantSubscriptionRequired($context) ? [static::getTenantSubscribedMiddleware($context)] : []),
            static::$routeMiddleware,
        );
    }

    public static function getEmailVerifiedMiddleware(Context $context): string
    {
        return $context->getEmailVerifiedMiddleware();
    }

    public static function isEmailVerificationRequired(Context $context): bool
    {
        return $context->isEmailVerificationRequired();
    }

    public static function getTenantSubscribedMiddleware(Context $context): string
    {
        return $context->getTenantBillingProvider()->getSubscribedMiddleware();
    }

    public static function isTenantSubscriptionRequired(Context $context): bool
    {
        return $context->isTenantSubscriptionRequired();
    }
}
