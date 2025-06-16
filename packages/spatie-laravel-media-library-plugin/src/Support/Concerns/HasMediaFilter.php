<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMediaFilter
{
    protected ?Closure $filterMediaUsing = null;

    public function filterMediaUsing(?Closure $callback): static
    {
        $this->filterMediaUsing = $callback;

        return $this;
    }

    /**
     * @param  Collection<array-key, Media>  $media
     * @return Collection<array-key, Media>
     */
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
