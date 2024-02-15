<?php

declare(strict_types=1);

namespace Filament\Tables\Columns;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class SpatieMediaLibraryImageColumn extends ImageColumn
{
    protected string | Closure | null $collection = null;

    protected string | Closure | null $conversion = null;

    protected mixed $mapColumn = null;

    public function applyEagerLoading(Builder | Relation $query): Builder | Relation
    {
        if ($this->isHidden()) {
            return $query;
        }

        if ($this->hasRelationship($query->getModel())) {
            return $query->with([
                "{$this->getRelationshipName()}.media",
            ]);
        }

        return $query->with(['media']);
    }

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

        if ($this->hasRelationship($record)) {
            $record = collect($this->getRelationshipResults($record))->first();
        }
        
        $media = $record->getFirstMedia(filters: fn(Media $media): bool => $media->uuid === $state);

        if ( ! $media) {
            return null;
        }

        $conversion = $this->getConversion();

        if ('private' === $this->getVisibility()) {
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

    public function getmapColumn(): mixed
    {
        return $this->evaluate($this->mapColumn) ?? 'uuid';
    }

    public function getStateFromRecord(): mixed
    {
        $record = $this->getRecord();

        if ($this->hasRelationship($record)) {
            $record = collect($this->getRelationshipResults($record))->first();
        }

        $state = $record->getMedia()
            ->pluck($this->getmapColumn())
            ->filter(fn($state): bool => filled($state))
            ->when($this->isDistinctList(), fn(Collection $state) => $state->unique())
            ->values();

        if ( ! $state->count()) {
            return null;
        }

        return $state->all();
    }

    public function mapColumn(mixed $column): static
    {
        $this->mapColumn = $column;

        return $this;
    }
}
