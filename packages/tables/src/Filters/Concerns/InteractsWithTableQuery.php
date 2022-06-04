<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait InteractsWithTableQuery
{
    protected ?Closure $modifyQueryUsing = null;

    protected array $globalScopesToRemove = [];

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

        $callback = $this->modifyQueryUsing;
        $callback($query, $data);

        return $query;
    }

    public function query(?Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    public function withoutGlobalScopes(array $scopes): static
    {
        $this->globalScopesToRemove = $scopes;

        return $this;
    }

    public function getGlobalScopesToRemove(): array
    {
        return $this->globalScopesToRemove;
    }

    protected function hasQueryModificationCallback(): bool
    {
        return $this->modifyQueryUsing instanceof Closure;
    }
}
