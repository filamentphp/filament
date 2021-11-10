<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Builder;

class SpatieMediaLibraryImageColumn extends ImageColumn
{
    protected ?string $collection = null;

    public function collection(string $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->collection ?? 'default';
    }

    public function getImagePath(): ?string
    {
        $state = $this->getState();

        if ($state) {
            return $state;
        }

        $media = $this->getRecord()
            ->getMedia($this->getCollection())
            ->first();

        return $media?->getUrl();
    }

    public function applyEagreLoading(Builder $query): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        return $query->with(['media']);
    }
}
