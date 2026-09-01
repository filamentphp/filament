<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Support\Collection;

trait HasMediaFilter
{
    protected ?Closure $filterMediaUsing = null;

    public function filterMediaUsing(?Closure $callback): static
    {
        $this->filterMediaUsing = $callback;

        return $this;
    }

    public function filterMedia(Collection $media): Collection
    {
        return $this->evaluate($this->filterMediaUsing, [
            'media' => $media,
        ]) ?? $media;
    }

    public function hasMediaFilter(): bool
    {
        return $this->filterMediaUsing instanceof Closure;
    }
}
