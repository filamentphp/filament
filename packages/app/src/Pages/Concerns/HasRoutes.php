<?php

namespace Filament\Pages\Concerns;

use Filament\Context;
use Illuminate\Support\Facades\Route;

trait HasRoutes
{
    protected static ?string $slug = null;

    /**
     * @var string | array<string>
     */
    protected static string | array $routeMiddleware = [];

    public static function routes(Context $context): void
    {
        $slug = static::getSlug();

        Route::get("/{$slug}", static::class)
            ->middleware(static::getRouteMiddleware($context))
            ->name((string) str($slug)->replace('/', '.'));
    }

    public static function getSlug(): string
    {
        return static::$slug ?? str(class_basename(static::class))
            ->kebab()
            ->slug();
    }

    /**
     * @return string | array<string>
     */
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
