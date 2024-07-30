<?php

namespace Filament\Panel\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

trait HasUrls
{
    /**
     * @param  array<mixed>  $parameters
     *
     * @throws Throwable
     */
    public function getResourceUrl(string | Model $model, string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?Model $tenant = null): string
    {
        $modelClass = is_string($model) ? $model : $model::class;

        $resource = $this->getModelResource($modelClass);

        if (blank($resource)) {
            throw new Exception("No Filament resource found for model {$modelClass}");
        }

        // If the model is an instance of Model and the name is "edit" or "view" pass the record as a parameter.
        if ($model instanceof Model && in_array($name, ['edit', 'view']) && blank($parameters)) {
            $parameters = ['record' => $model];
        }

        return $resource::getUrl($name, $parameters, $isAbsolute, $this->getId(), $tenant);
    }
}
