<?php

namespace Filament\Resources\Pages;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Pages\Page as BasePage;
use Filament\Panel;
use Filament\Resources\Events\RecordCreated;
use Filament\Resources\Events\RecordSaved;
use Filament\Resources\Events\RecordUpdated;
use Filament\Resources\Pages\Concerns\CanAuthorizeResourceAccess;
use Filament\Resources\Pages\Concerns\InteractsWithParentRecord;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route as RouteFacade;
use LogicException;

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
    public function getResourceUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = true): string
    {
        if (filled($name) && ($name !== 'index') && method_exists($this, 'getRecord')) {
            $parameters['record'] ??= $this->getRecord();
        }

        return static::getResource()::getUrl($name, $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
    }

    public static function getRouteName(?Panel $panel = null): string
    {
        $panel ??= Filament::getCurrentOrDefaultPanel();

        $routeBaseName = static::getResource()::getRouteBaseName($panel);

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
                ->isActiveWhen(fn (): bool => original_request()->routeIs(static::getNavigationItemActiveRoutePattern()))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->url(static::getNavigationUrl($urlParameters)),
        ];
    }

    /**
     * @return string | array<string>
     */
    public static function getNavigationItemActiveRoutePattern(): string | array
    {
        return static::getRouteName();
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function getNavigationUrl(array $parameters = []): string
    {
        return static::getUrl($parameters, shouldGuessMissingParameters: true);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        return static::getResource()::getUrl(static::getResourcePageName(), $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
    }

    public static function getResourcePageName(): string
    {
        foreach (static::getResource()::getPages() as $pageName => $pageRegistration) {
            if ($pageRegistration->getPage() !== static::class) {
                continue;
            }

            return $pageName;
        }

        throw new LogicException('Page [' . static::class . '] is not registered to the resource [' . static::getResource() . '].');
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
    public function getResourceBreadcrumbs(): array
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
                        $parentResource::getUrl('view', ['record' => $parentRecord], shouldGuessMissingParameters: true) => $parentRecordTitle,
                        ...$breadcrumbs,
                    ];
                } elseif ($parentResource::hasPage('edit') && $parentResource::canEdit($parentRecord)) {
                    $breadcrumbs = [
                        $parentResource::getUrl('edit', ['record' => $parentRecord], shouldGuessMissingParameters: true) => $parentRecordTitle,
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
                    ], shouldGuessMissingParameters: true) => $parentResource::getBreadcrumb(),
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
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            ...$this->getResourceBreadcrumbs(),
            $this->getBreadcrumb(),
        ];
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

    public function getDefaultActionAuthorizationResponse(Action $action): ?Response
    {
        return match (true) {
            $action instanceof CreateAction => static::getResource()::getCreateAuthorizationResponse(),
            $action instanceof DeleteAction => static::getResource()::getDeleteAuthorizationResponse($action->getRecord()),
            $action instanceof EditAction => static::getResource()::getEditAuthorizationResponse($action->getRecord()),
            $action instanceof ForceDeleteAction => static::getResource()::getForceDeleteAuthorizationResponse($action->getRecord()),
            $action instanceof ReplicateAction => static::getResource()::getReplicateAuthorizationResponse($action->getRecord()),
            $action instanceof RestoreAction => static::getResource()::getRestoreAuthorizationResponse($action->getRecord()),
            $action instanceof ViewAction => static::getResource()::getViewAuthorizationResponse($action->getRecord()),
            $action instanceof DeleteBulkAction => static::getResource()::getDeleteAnyAuthorizationResponse(),
            $action instanceof ForceDeleteBulkAction => static::getResource()::getForceDeleteAnyAuthorizationResponse(),
            $action instanceof RestoreBulkAction => static::getResource()::getRestoreAnyAuthorizationResponse(),
            default => null,
        };
    }

    public function getDefaultActionIndividualRecordAuthorizationResponseResolver(Action $action): ?Closure
    {
        return match (true) {
            $action instanceof DeleteBulkAction => fn (Model $record): Response => static::getResource()::getDeleteAuthorizationResponse($record),
            $action instanceof ForceDeleteBulkAction => fn (Model $record): Response => static::getResource()::getForceDeleteAuthorizationResponse($record),
            $action instanceof RestoreBulkAction => fn (Model $record): Response => static::getResource()::getRestoreAuthorizationResponse($record),
            default => null,
        };
    }

    /**
     * @return ?class-string<Model>
     */
    public function getDefaultActionModel(Action $action): ?string
    {
        return $this->getModel();
    }

    public function getDefaultActionModelLabel(Action $action): ?string
    {
        return $this->getModelLabel() ?? static::getResource()::getModelLabel();
    }

    public function getDefaultActionRelationship(Action $action): ?Relation
    {
        if (
            ($action instanceof CreateAction) &&
            ($parentRecord = $this->getParentRecord())
        ) {
            return static::getResource()::getParentResourceRegistration()->getRelationship($parentRecord);
        }

        return null;
    }

    public function getDefaultActionUrl(Action $action): ?string
    {
        $actionModel = $action->getModel();

        if (
            ($action instanceof CreateAction) &&
            (static::getResource()::hasPage('create')) &&
            (blank($actionModel) || ($actionModel === static::getResource()::getModel()))
        ) {
            return $this->getResourceUrl('create');
        }

        if (
            ($action instanceof EditAction) &&
            (static::getResource()::hasPage('edit')) &&
            (! $this instanceof EditRecord) &&
            (blank($actionModel) || ($actionModel === static::getResource()::getModel()))
        ) {
            return $this->getResourceUrl('edit', ['record' => $action->getRecord()]);
        }

        if (
            ($action instanceof ViewAction) &&
            (static::getResource()::hasPage('view')) &&
            (! $this instanceof ViewRecord) &&
            (blank($actionModel) || ($actionModel === static::getResource()::getModel()))
        ) {
            return $this->getResourceUrl('view', ['record' => $action->getRecord()]);
        }

        return null;
    }

    /**
     * @deprecated Override the resource's `getModelLabel()` method to configure the model label.
     */
    public function getModelLabel(): ?string
    {
        return null;
    }

    protected function afterActionCalled(Action $action): void
    {
        if ($action instanceof CreateAction) {
            Event::dispatch(RecordCreated::class, ['record' => $action->getRecord(), 'data' => $action->getData(), 'page' => $this]);
            Event::dispatch(RecordSaved::class, ['record' => $action->getRecord(), 'data' => $action->getData(), 'page' => $this]);
        }

        if ($action instanceof EditAction) {
            Event::dispatch(RecordUpdated::class, ['record' => $action->getRecord(), 'data' => $action->getData(), 'page' => $this]);
            Event::dispatch(RecordSaved::class, ['record' => $action->getRecord(), 'data' => $action->getData(), 'page' => $this]);
        }
    }
}
