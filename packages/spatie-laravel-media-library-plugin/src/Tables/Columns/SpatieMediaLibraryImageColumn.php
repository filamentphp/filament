<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SpatieMediaLibraryImageColumn extends ImageColumn
{
    protected ?string $collection = null;

    protected string $conversion = '';

    public function collection(string $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function conversion(string $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->collection ?? 'default';
    }

    public function getConversion(Media $media): string
    {
        return $media->hasGeneratedConversion($this->conversion) ? $this->conversion : '';
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

        return $media?->getUrl($this->getConversion($media));
    }

    public function applyEagreLoading(Builder $query): Builder
    {
        if ($this->isHidden()) {
            return $query;
        }

        return $query->with(['media']);
    }
}
