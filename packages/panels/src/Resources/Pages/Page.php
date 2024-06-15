<?php

namespace Filament\Resources\Pages;

use Exception;
use Filament\Clusters\Cluster;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page as BasePage;
use Filament\Panel;
use Filament\Resources\Pages\Concerns\CanAuthorizeResourceAccess;
use Filament\Resources\Pages\Concerns\InteractsWithParentRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

use function Filament\Support\original_request;

abstract class Page extends BasePage
{
    use CanAuthorizeResourceAccess;
    use InteractsWithParentRecord;

    protected static ?string $breadcrumb = null;

    protected static string $resource;

    protected static bool $isDiscovered = false;

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function getResourceUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        if (method_exists($this, 'getRecord')) {
            $parameters['record'] ??= $this->getRecord();
        }

        if ($parentResourceRegistration = static::getResource()::getParentResourceRegistration()) {
            $parameters[$parentResourceRegistration->getParentRouteParameterName()] ??= $this->getParentRecord();
        }

        return static::getResource()::getUrl($name, $parameters, $isAbsolute, $panel, $tenant);
    }

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
                ->isActiveWhen(fn (): bool => original_request()->routeIs(static::getRouteName()))
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

    public function hasResourceBreadcrumbs(): bool
    {
        return true;
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        if ($this->hasResourceBreadcrumbs()) {
            $resource = static::getResource();

            $breadcrumbs[$this->getResourceUrl()] = $resource::getBreadcrumb();

            $parentResourceRegistration = $resource::getParentResourceRegistration();
            $parentResource = $parentResourceRegistration?->getParentResource();
            $parentRecord = $this->getParentRecord();

            while ($parentResourceRegistration && $parentRecord) {
                $parentRecordTitle = $parentResource::hasRecordTitle() ?
                    $parentResource::getRecordTitle($parentRecord) :
                    $parentResource::getTitleCaseModelLabel();

                if ($parentResource::hasPage('view') && $parentResource::canView($parentRecord)) {
                    $breadcrumbs = [
                        $parentResource::getUrl('view', ['record' => $parentRecord]) => $parentRecordTitle,
                        ...$breadcrumbs,
                    ];
                } elseif ($parentResource::hasPage('edit') && $parentResource::canEdit($parentRecord)) {
                    $breadcrumbs = [
                        $parentResource::getUrl('edit', ['record' => $parentRecord]) => $parentRecordTitle,
                        ...$breadcrumbs,
                    ];
                } else {
                    $breadcrumbs = [
                        $parentRecordTitle,
                        ...$breadcrumbs,
                    ];
                }

                $breadcrumbs = [
                    $parentResource::getUrl(null, [
                        'record' => $parentRecord,
                    ]) => $parentResource::getBreadcrumb(),
                    ...$breadcrumbs,
                ];

                $parentResourceRegistration = $parentResource::getParentResourceRegistration();

                if ($parentResourceRegistration) {
                    $parentResource = $parentResourceRegistration->getParentResource();
                    $parentRecord = $parentRecord->{$parentResourceRegistration->getInverseRelationshipName()};
                }
            }
        }

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

    public static function getSubNavigationPosition(): SubNavigationPosition
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
