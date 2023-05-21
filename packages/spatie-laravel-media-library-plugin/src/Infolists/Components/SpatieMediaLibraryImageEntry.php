<?php

namespace Filament\Infolists\Components;

use Illuminate\Support\Collection;
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

    public function getImagePath(): ?string
    {
        $state = $this->getState();

        if ($state && (! $state instanceof Collection)) {
            return $state;
        }

        $record = $this->getRecord();

        if (! $record) {
            return null;
        }

        $relationshipName = $this->getRelationshipName();

        if (filled($relationshipName)) {
            $record = $record->getRelationValue($relationshipName);
        }

        if ($this->getVisibility() === 'private' && method_exists($record, 'getFirstTemporaryUrl')) {
            try {
                return $record->getFirstTemporaryUrl(
                    now()->addMinutes(5),
                    $this->getCollection(),
                    $this->getConversion(),
                );
            } catch (Throwable $exception) {
                // This driver does not support creating temporary URLs.
            }
        }

        if (! method_exists($record, 'getFirstMediaUrl')) {
            return $state;
        }

        $firstMediaUrl = $record->getFirstMediaUrl($this->getCollection(), $this->getConversion());

        return filled($firstMediaUrl) ? $firstMediaUrl : $this->getDefaultImageUrl();
    }
}
