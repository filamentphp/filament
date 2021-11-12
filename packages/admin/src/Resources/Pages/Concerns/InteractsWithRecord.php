<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait InteractsWithRecord
{
    protected function getRecord($key): Model
    {
        $record = static::getResource()::resolveRecordRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel(static::getModel(), [$key]);
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
