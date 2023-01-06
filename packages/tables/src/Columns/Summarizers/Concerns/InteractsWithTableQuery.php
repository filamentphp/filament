<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait InteractsWithTableQuery
{
    protected ?Closure $modifyQueryUsing = null;

    protected ?Builder $query = null;

    protected ?Closure $scopeQueryToGroupUsing = null;

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

    public function scopeQueryToGroupUsing(?Closure $callback): static
    {
        $this->scopeQueryToGroupUsing = $callback;

        return $this;
    }

    public function scopeQueryToGroup(Builder $query): Builder
    {
        return $this->evaluate($this->scopeQueryToGroupUsing, [
            'query' => $query,
        ]) ?? $query;
    }

    public function getQuery(): ?Builder
    {
        return $this->query;
    }

    public function hasQueryModificationCallback(): bool
    {
        return $this->modifyQueryUsing instanceof Closure;
    }
}
