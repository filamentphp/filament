<?php

namespace Filament\Resources\Pages;

use Closure;
use Filament\Facades\Filament;
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
            route: fn (): Route => RouteFacade::get($path, static::class)
                ->middleware(static::getMiddleware()),
        );
    }

    public static function getTenantSubscribedMiddleware(): string
    {
        return static::getResource()::getTenantSubscribedMiddleware();
    }

    public static function isTenantSubscriptionRequired(): bool
    {
        return static::getResource()::isTenantSubscriptionRequired();
    }

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? static::getTitle();
    }

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

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }
}
