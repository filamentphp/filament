<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait InteractsWithTableQuery
{
    protected ?Closure $modifyQueryUsing = null;

    /**
     * @param  array<string, mixed>  $data
     */
    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        if (! $this->hasQueryModificationCallback()) {
            return $query;
        }

        if (! ($data['isActive'] ?? true)) {
            return $query;
        }

        $this->evaluate($this->modifyQueryUsing, [
            'data' => $data,
            'query' => $query,
            'state' => $data,
        ]);

        return $query;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function applyToBaseQuery(Builder $query, array $data = []): Builder
    {
        return $query;
    }

    public function query(?Closure $callback): static
    {
        $this->modifyQueryUsing($callback);

        return $this;
    }

    public function modifyQueryUsing(?Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    protected function hasQueryModificationCallback(): bool
    {
        return $this->modifyQueryUsing instanceof Closure;
    }
}
