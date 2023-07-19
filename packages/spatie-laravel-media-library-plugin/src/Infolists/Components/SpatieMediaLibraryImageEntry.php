<?php

namespace Filament\Infolists\Components;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class SpatieMediaLibraryImageEntry extends ImageEntry
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

    /**
     * @return array<string>
     */
    public function getState(): array
    {
        $collection = $this->getCollection();

        return $this->getRecord()->getRelationValue('media')
            ->filter(fn (Media $media): bool => blank($collection) || ($media->getAttributeValue('collection_name') === $collection))
            ->map(fn (Media $media): string => $media->uuid)
            ->all();
    }
}
