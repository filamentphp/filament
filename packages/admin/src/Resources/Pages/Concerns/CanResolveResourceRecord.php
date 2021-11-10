<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CanResolveResourceRecord
{
    protected function resolveRecord($key): Model
    {
        $model = static::getModel();

        $record = (new $model())->resolveRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($model, [$key]);
        }

        return $record;
    }

    protected function getRecordTitle(): ?string
    {
        $resource = static::getResource();

        if (! $resource::hasRecordTitle()) {
            return null;
        }

        return $resource::getRecordTitle($this->record);
    }
}
