<?php

namespace Filament\Resources\Resource\Concerns;

use Exception;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

use function Filament\Support\original_request;

trait CanGenerateUrls
{
    /**
     * @param  array<mixed>  $parameters
     */
    public static function getUrl(?string $name = null, array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        if ($shouldGuessMissingParameters) {
            $originalRequestRoute = null;
            $parentResources = [];
            $parentResource = static::getParentResourceRegistration();

            while (filled($parentResource)) {
                array_unshift($parentResources, $parentResource);

                $parentResource = $parentResource->getParentResource()::getParentResourceRegistration();
            }

            foreach ($parentResources as $parentResource) {
                $parentRouteParameterName = $parentResource->getParentRouteParameterName();

                if (filled($parameters[$parentRouteParameterName] ?? null)) {
                    continue;
                }

                $originalRequestRoute ??= original_request()->route();

                if (! $originalRequestRoute->hasParameter($parentRouteParameterName)) {
                    $parameters[$parentRouteParameterName] = $originalRequestRoute->parameter('record');

                    break;
                }

                $parameters[$parentRouteParameterName] = $originalRequestRoute->parameter($parentRouteParameterName);
            }
        }

        if (blank($name)) {
            return static::getIndexUrl($parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
        }

        if (blank($panel) || ($panel = Filament::getPanel($panel))->hasTenancy()) {
            $parameters['tenant'] ??= ($tenant ?? Filament::getTenant());
        }

        $routeBaseName = static::getRouteBaseName($panel);

        return route("{$routeBaseName}.{$name}", $parameters, $isAbsolute);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        $parentResourceRegistration = static::getParentResourceRegistration();

        if ($parentResourceRegistration) {
            $parentResource = $parentResourceRegistration->getParentResource();
            $parentRouteParameterName = $parentResourceRegistration->getParentRouteParameterName();

            $record = $parameters[$parentRouteParameterName] ?? null;
            unset($parameters[$parentRouteParameterName]);

            if ($parentResource::hasPage($relationshipPageName = $parentResourceRegistration->getRouteName())) {
                return $parentResource::getUrl($relationshipPageName, [
                    ...$parameters,
                    'record' => $record,
                ], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
            }

            if ($parentResource::hasPage('view')) {
                return $parentResource::getUrl('view', [
                    'relation' => $parentResourceRegistration->getRelationshipName(),
                    ...$parameters,
                    'record' => $record,
                ], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
            }

            if ($parentResource::hasPage('edit')) {
                return $parentResource::getUrl('edit', [
                    'relation' => $parentResourceRegistration->getRelationshipName(),
                    ...$parameters,
                    'record' => $record,
                ], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
            }

            if ($parentResource::hasPage('index')) {
                return $parentResource::getUrl('index', $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
            }
        }

        if (! static::hasPage('index')) {
            throw new Exception('The resource [' . static::class . '] does not have an [index] page. Define [getIndexUrl()] for alternative routing.');
        }

        return static::getUrl('index', $parameters, $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
    }
}
