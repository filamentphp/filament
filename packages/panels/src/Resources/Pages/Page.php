<?php

namespace Filament\Resources\Pages;

use Exception;
use Filament\Clusters\Cluster;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page as BasePage;
use Filament\Pages\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\Concerns\CanAuthorizeResourceAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

abstract class Page extends BasePage
{
    use CanAuthorizeResourceAccess;

    protected static ?string $breadcrumb = null;

    protected static string $resource;

    protected static bool $isDiscovered = false;

    public static function getRouteName(?string $panel = null): string
    {
        $routeBaseName = static::getResource()::getRouteBaseName(panel: $panel);

        return $routeBaseName . '.' . static::getResourcePageName();
    }

    /**
     * @param  array<string, mixed>  $urlParameters
     */
    public static function getNavigationItems(array $urlParameters = []): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->parentItem(static::getNavigationParentItem())
                ->icon(static::getNavigationIcon())
                ->activeIcon(static::getActiveNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteName()))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->url(static::getNavigationUrl($urlParameters)),
        ];
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function getNavigationUrl(array $parameters = []): string
    {
        return static::getUrl($parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        return static::getResource()::getUrl(static::getResourcePageName(), $parameters, $isAbsolute, $panel, $tenant);
    }

    public static function getResourcePageName(): string
    {
        foreach (static::getResource()::getPages() as $pageName => $pageRegistration) {
            if ($pageRegistration->getPage() !== static::class) {
                continue;
            }

            return $pageName;
        }

        throw new Exception('Page [' . static::class . '] is not registered to the resource [' . static::getResource() . '].');
    }

    public static function route(string $path): PageRegistration
    {
        return new PageRegistration(
            page: static::class,
            route: fn (Panel $panel): Route => RouteFacade::get($path, static::class)
                ->middleware(static::getRouteMiddleware($panel))
                ->withoutMiddleware(static::getWithoutRouteMiddleware($panel)),
        );
    }

    public static function getEmailVerifiedMiddleware(Panel $panel): string
    {
        return static::getResource()::getEmailVerifiedMiddleware($panel);
    }

    public static function isEmailVerificationRequired(Panel $panel): bool
    {
        return static::getResource()::isEmailVerificationRequired($panel);
    }

    public static function getTenantSubscribedMiddleware(Panel $panel): string
    {
        return static::getResource()::getTenantSubscribedMiddleware($panel);
    }

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return static::getResource()::isTenantSubscriptionRequired($panel);
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

        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return parent::shouldRegisterNavigation();
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess();
    }

    /**
     * @return class-string<Model>
     */
    public function getModel(): string
    {
        return static::getResource()::getModel();
    }

    /**
     * @return class-string
     */
    public static function getResource(): string
    {
        return static::$resource;
    }

    /**
     * @return array<string>
     */
    public function getRenderHookScopes(): array
    {
        return [
            ...parent::getRenderHookScopes(),
            static::getResource(),
        ];
    }

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return static::getResource()::getSubNavigationPosition();
    }

    /**
     * @return class-string<Cluster> | null
     */
    public static function getCluster(): ?string
    {
        return static::getResource()::getCluster();
    }

    public function getSubNavigation(): array
    {
        return [];
    }
}
