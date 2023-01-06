<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait InteractsWithTableQuery
{
    protected ?Closure $modifyQueryUsing = null;

    protected ?Builder $query = null;

    public function query(Builder | Closure | null $query): static
    {
        if ($query instanceof Builder || ($query === null)) {
            $this->query = $query;
        }

        if ($query instanceof Closure || ($query === null)) {
            $this->modifyQueryUsing = $query;
        }

        return $this;
    }

    public function getQuery(): ?Builder
    {
        return $this->query;
    }

    public function hasQueryModification(): bool
    {
        return $this->modifyQueryUsing instanceof Closure;
    }
}
