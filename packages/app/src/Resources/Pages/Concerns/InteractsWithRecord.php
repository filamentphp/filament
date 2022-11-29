<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

trait InteractsWithRecord
{
    public Model | int | string $record;

    protected function resolveRecord(int | string $key): Model
    {
        $record = static::getResource()::resolveRecordRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($this->getModel(), [$key]);
        }

        return $record;
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public function getRecordTitle(): string
    {
        $resource = static::getResource();

        if (! $resource::hasRecordTitle()) {
            return Str::headline($resource::getModelLabel());
        }

        return $resource::getRecordTitle($this->getRecord());
    }
}
