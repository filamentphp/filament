<?php

namespace Filament\Infolists\Components;

use Closure;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class SpatieMediaLibraryImageEntry extends ImageEntry
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

        if (! $record) {
            return null;
        }

        $relationshipName = $this->getRelationshipName();

        if (filled($relationshipName)) {
            $record = $record->getRelationValue($relationshipName);
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

        return $this->getRecord()->getRelationValue('media')
            ->filter(fn (Media $media): bool => blank($collection) || ($media->getAttributeValue('collection_name') === $collection))
            ->sortBy('order_column')
            ->map(fn (Media $media): string => $media->uuid)
            ->all();
    }
}
