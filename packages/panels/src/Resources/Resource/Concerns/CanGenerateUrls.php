<?php

namespace Filament\Resources\Resource\Concerns;

use Exception;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

trait CanGenerateUrls
{
    /**
     * @param  array<mixed>  $parameters
     */
    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $record = $parameters['record'] ?? null;
        $parentResource = static::getParentResourceRegistration();

        while (filled($parentResource)) {
            $record = $parameters[$parentResource->getParentRouteParameterName()] ?? $record?->{$parentResource->getInverseRelationshipName()};
            $parameters[$parentResource->getParentRouteParameterName()] ??= $record;
            $parameters['record'] ??= $record;

            $parentResource = $parentResource->getParentResource()::getParentResourceRegistration();
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
