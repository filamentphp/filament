<?php

namespace Filament\Tables\Columns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class SpatieMediaLibraryImageColumn extends ImageColumn
{
    protected string | Closure | null $collection = null;

    protected string | Closure | null $conversion = null;

    public function collection(string | Closure | null $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function conversion(string | Closure | null $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->evaluate($this->collection) ?? 'default';
    }

    public function getConversion(): ?string
    {
        return $this->evaluate($this->conversion);
    }

    public function getImageUrl(?string $state = null): ?string
    {
        $record = $this->getRecord();

        if ($this->queriesRelationships($record)) {
            $record = $record->getRelationValue($this->getRelationshipName());
        }

        /** @var ?Media $media */
        $media = $record->media->first(fn (Media $media): bool => $media->uuid === $state);

        if (! $media) {
            return null;
        }

        $conversion = $this->getConversion();

        if ($this->getVisibility() === 'private') {
            try {
                return $media->getTemporaryUrl(
                    now()->addMinutes(5),
                    $conversion ?? '',
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        return $media->getAvailableUrl(Arr::wrap($conversion));
    }

    /**
     * @return array<string>
     */
    public function getState(): array
    {
        $collection = $this->getCollection();

        $record = $this->getRecord();

        if ($this->queriesRelationships($record)) {
            $record = $record->getRelationValue($this->getRelationshipName());
        }

        return $record->getRelationValue('media')
            ->filter(fn (Media $media): bool => blank($collection) || ($media->getAttributeValue('collection_name') === $collection))
            ->sortBy('order_column')
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
                "{$this->getRelationshipName()}.media" => fn (Builder | Relation $query) => $query->when(
                    $this->getCollection(),
                    fn (Builder | Relation $query, string $collection) => $query->where(
                        'collection_name',
                        $collection,
                    ),
                ),
            ]);
        }

        return $query->with(['media']);
    }
}
