<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

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

    public function getConversion(): string
    {
        return $this->conversion ?? '';
    }

    public function getImageUrl(?string $state = null): ?string
    {
        $record = $this->getRecord();

        if ($this->queriesRelationships($record)) {
            $record = $record->getRelationValue($this->getRelationshipName());
        }

        /** @var Media $media */
        $media = $record->media->first(fn (Media $media) => $media->uuid === $state);

        if (! $media) {
            return null;
        }

        if ($this->getVisibility() === 'private') {
            try {
                return $media->getTemporaryUrl(
                    now()->addMinutes(5),
                    $this->getConversion(),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $media->getUrl($this->getConversion());
    }

    public function getState(): array
    {
        $collection = $this->getCollection();

        return $this->getRecord()->media
            ->filter(fn (Media $media): bool => blank($collection) || ($media->collection_name === $collection))
            ->map(fn (Media $media): string => $media->uuid)
            ->all();
    }

    public function applyEagerLoading(Builder | Relation $query): Builder | Relation
    {
        if ($this->isHidden()) {
            return $query;
        }

        if ($this->queriesRelationships($query->getModel())) {
            return $query->with([
                "{$this->getRelationshipName()}.media" => fn (Builder $query) => $query->when(
                    $this->getCollection(),
                    fn (Builder $query, string $collection) => $query->where(
                        'collection_name', $collection,
                    ),
                ),
            ]);
        }

        return $query->with(['media']);
    }

    public function getRecord(): Model&HasMedia
    {
        return parent::getRecord();
    }
}
