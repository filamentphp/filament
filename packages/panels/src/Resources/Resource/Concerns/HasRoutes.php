<?php

namespace Filament\Resources\Resource\Concerns;

use Closure;
use Exception;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Stringable;

trait HasRoutes
{
    protected static ?string $slug = null;

    protected static ?string $recordRouteKeyName = null;

    /**
     * @var string | array<string>
     */
    protected static string | array $routeMiddleware = [];

    /**
     * @var string | array<string>
     */
    protected static string | array $withoutRouteMiddleware = [];

    public static function resolveRecordRouteBinding(int | string $key): ?Model
    {
        return app(static::getModel())
            ->resolveRouteBindingQuery(static::getEloquentQuery(), $key, static::getRecordRouteKeyName())
            ->first();
    }

    public static function getRouteBaseName(?string $panel = null): string
    {
        if ($parentResource = static::getParentResourceRegistration()) {
            return $parentResource->getParentResource()::getRouteBaseName($panel) . '.' . $parentResource->getRouteName();
        }

        $panel = $panel ? Filament::getPanel($panel) : Filament::getCurrentPanel();

        $routeBaseName = (string) str(static::getSlug())
            ->replace('/', '.')
            ->prepend('resources.');

        if (filled($cluster = static::getCluster())) {
            $routeBaseName = $cluster::prependClusterRouteBaseName($routeBaseName);
        }

        return $panel->generateRouteName($routeBaseName);
    }

    public static function getRecordRouteKeyName(): ?string
    {
        return static::$recordRouteKeyName;
    }

    public static function registerRoutes(Panel $panel): void
    {
        if ($parentResource = static::getParentResourceRegistration()) {
            $parentResource->getParentResource()::registerNestedRoutes($panel, function () use ($panel, $parentResource) {
                Route::name($parentResource->getRouteName() . '.')
                    ->prefix('{' . $parentResource->getParentRouteParameterName() . '}/' . $parentResource->getSlug())
                    ->group(function () use ($panel) {
                        foreach (static::getPages() as $name => $page) {
                            $page->registerRoute($panel)?->name($name);
                        }
                    });
            });

            return;
        }

        if (filled($cluster = static::getCluster())) {
            Route::name($cluster::prependClusterRouteBaseName('resources.'))
                ->prefix($cluster::prependClusterSlug(''))
                ->group(fn () => static::routes($panel));

            return;
        }

        Route::name('resources.')->group(fn () => static::routes($panel));
    }

    public static function routes(Panel $panel): void
    {
        Route::name(static::getRelativeRouteName() . '.')
            ->prefix(static::getRoutePrefix())
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->group(function () use ($panel) {
                foreach (static::getPages() as $name => $page) {
                    $page->registerRoute($panel)?->name($name);
                }
            });
    }

    public static function registerNestedRoutes(Panel $panel, Closure $routes): void
    {
        if ($parentResource = static::getParentResourceRegistration()) {
            $parentResource->getParentResource()::registerNestedRoutes($panel, function () use ($parentResource, $routes) {
                Route::name($parentResource->getRouteName() . '.')
                    ->prefix('{' . $parentResource->getParentRouteParameterName() . '}/' . $parentResource->getSlug())
                    ->group($routes);
            });

            return;
        }

        $nestedRoutes = fn () => Route::name(static::getRelativeRouteName() . '.')
            ->prefix(static::getRoutePrefix())
            ->middleware(static::getRouteMiddleware($panel))
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->group($routes);

        if (filled($cluster = static::getCluster())) {
            Route::name($cluster::prependClusterRouteBaseName('resources.'))
                ->prefix($cluster::prependClusterSlug(''))
                ->group($nestedRoutes);

            return;
        }

        Route::name('resources.')->group($nestedRoutes);
    }

    public static function getRelativeRouteName(): string
    {
        return (string) str(static::getSlug())->replace('/', '.');
    }

    public static function getRoutePrefix(): string
    {
        return static::getSlug();
    }

    /**
     * @return string | array<string>
     */
    public static function getRouteMiddleware(Panel $panel): string | array
    {
        return static::$routeMiddleware;
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

    public static function getTenantSubscribedMiddleware(Panel $panel): string
    {
        return $panel->getTenantBillingProvider()->getSubscribedMiddleware();
    }

    public static function getSlug(): string
    {
        if (filled(static::$slug)) {
            return static::$slug;
        }

        return str(static::class)
            ->whenContains(
                '\\Resources\\',
                fn (Stringable $slug): Stringable => $slug->afterLast('\\Resources\\'),
                fn (Stringable $slug): Stringable => $slug->classBasename(),
            )
            ->beforeLast('Resource')
            ->plural()
            ->explode('\\')
            ->map(fn (string $string) => str($string)->kebab()->slug())
            ->implode('/');
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $record = $parameters['record'] ?? null;
        $parentResource = static::getParentResource();

        while (filled($parentResource)) {
            $record = $record?->{$parentResource->getInverseRelationshipName()};
            $parameters[$parentResource->getParentRouteParameterName()] ??= $record;
            $parameters['record'] ??= $record;

            $parentResource = $parentResource->getParentResource()::getParentResource();
        }

        if (blank($name)) {
            return static::getIndexUrl($parameters, $isAbsolute, $panel, $tenant);
        }

        if (blank($panel) || Filament::getPanel($panel)->hasTenancy()) {
            $parameters['tenant'] ??= ($tenant ?? Filament::getTenant());
        }

        $routeBaseName = static::getRouteBaseName(panel: $panel);

        return route("{$routeBaseName}.{$name}", $parameters, $isAbsolute);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $parentResourceRegistration = static::getParentResource();

        if ($parentResourceRegistration) {
            $parentResource = $parentResourceRegistration->getParentResource();
            $parentRouteParameterName = $parentResourceRegistration->getParentRouteParameterName();

            $record = $parameters[$parentRouteParameterName] ?? null;
            unset($parameters[$parentRouteParameterName]);

            if ($parentResource::hasPage($relationshipPageName = $parentResourceRegistration->getRouteName())) {
                return $parentResource::getUrl($relationshipPageName, [
                    ...$parameters,
                    'record' => $record,
                ], $isAbsolute, $panel, $tenant);
            }

            if ($parentResource::hasPage('view')) {
                return $parentResource::getUrl('view', [
                    'activeRelationManager' => $parentResourceRegistration->getRelationshipName(),
                    ...$parameters,
                    'record' => $record,
                ], $isAbsolute, $panel, $tenant);
            }

            if ($parentResource::hasPage('edit')) {
                return $parentResource::getUrl('edit', [
                    'activeRelationManager' => $parentResourceRegistration->getRelationshipName(),
                    ...$parameters,
                    'record' => $record,
                ], $isAbsolute, $panel, $tenant);
            }

            if ($parentResource::hasPage('index')) {
                return $parentResource::getUrl('index', $parameters, $isAbsolute, $panel, $tenant);
            }
        }

        if (! static::hasPage('index')) {
            throw new Exception('The resource [' . static::class . '] does not have an [index] page or define [getIndexUrl()] for alternative routing.');
        }

        return static::getUrl('index', $parameters, $isAbsolute, $panel, $tenant);
    }
}
