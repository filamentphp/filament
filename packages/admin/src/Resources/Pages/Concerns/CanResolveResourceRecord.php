<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CanResolveResourceRecord
{
    protected function resolveRecord($key): Model
    {
        $model = static::getResource()::getModel();

        $record = (new $model())->resolveRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($model, [$key]);
        }

        return $record;
    }
}
