<?php

namespace Filament\Resources\Pages;

use Filament\Context;
use Filament\Pages\Page as BasePage;
use Filament\Resources\Resource;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

abstract class Page extends BasePage
{
    protected static ?string $breadcrumb = null;

    protected static string $resource;

    public static function route(string $path): PageRegistration
    {
        return new PageRegistration(
            page: static::class,
            route: fn (Context $context): Route => RouteFacade::get($path, static::class)
                ->middleware(static::getRouteMiddleware($context)),
        );
    }

    public static function getEmailVerifiedMiddleware(Context $context): string
    {
        return static::getResource()::getEmailVerifiedMiddleware($context);
    }

    public static function isEmailVerificationRequired(Context $context): bool
    {
        return static::getResource()::isEmailVerificationRequired($context);
    }

    public static function getTenantSubscribedMiddleware(Context $context): string
    {
        return static::getResource()::getTenantSubscribedMiddleware($context);
    }

    public static function isTenantSubscriptionRequired(Context $context): bool
    {
        return static::getResource()::isTenantSubscriptionRequired($context);
    }

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? static::getTitle();
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumb = $this->getBreadcrumb();

        return array_merge(
            [$resource::getUrl() => $resource::getBreadcrumb()],
            (filled($breadcrumb) ? [$breadcrumb] : []),
        );
    }

    public static function authorizeResourceAccess(): void
    {
        abort_unless(static::getResource()::canViewAny(), 403);
    }

    public function getModel(): string
    {
        return static::getResource()::getModel();
    }

    /**
     * @return class-string<resource>
     */
    public static function getResource(): string
    {
        return static::$resource;
    }
}
