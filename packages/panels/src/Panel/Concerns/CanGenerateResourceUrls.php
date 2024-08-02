<?php

namespace Filament\Panel\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait CanGenerateResourceUrls
{
    /**
     * @param  array<mixed>  $parameters
     */
    public function getResourceUrl(string | Model $model, string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?Model $tenant = null): string
    {
        $modelClass = is_string($model) ? $model : $model::class;

        $resource = $this->getModelResource($modelClass) ?? throw new Exception("No Filament resource found for model [{$modelClass}].");

        if (
            ($model instanceof Model) &&
            in_array($name, ['edit', 'view'])
        ) {
            $parameters['record'] ??= $model;
        }

        return $resource::getUrl($name, $parameters, $isAbsolute, $this->getId(), $tenant);
    }
}
