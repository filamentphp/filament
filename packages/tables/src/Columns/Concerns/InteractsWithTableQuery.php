<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait InteractsWithTableQuery
{
    public function applyQueryModifications(Builder $query): Builder
    {
        if ($this->queriesRelationships()) {
            $query->with([$this->getRelationshipName()]);
        }

        return $query;
    }

    protected function getRelationshipName(): string
    {
        return Str::of($this->getName())->beforeLast('.');
    }

    protected function queriesRelationships(): bool
    {
        return Str::of($this->getName())->contains('.');
    }
}
