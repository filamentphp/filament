<?php

namespace Filament\Tables\Columns;

use Closure;
use Filament\SpatieLaravelMediaLibraryPlugin\Collections\AllMediaCollections;
use Filament\Support\Concerns\HasMediaFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class SpatieMediaLibraryImageColumn extends ImageColumn
{
    use HasMediaFilter;

    protected string | AllMediaCollections | Closure | null $collection = null;

    protected string | Closure | null $conversion = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultImageUrl(function (SpatieMediaLibraryImageColumn $column, Model $record): ?string {
            if ($column->hasRelationship($record)) {
                $record = $column->getRelationshipResults($record);
            }

            $records = Arr::wrap($record);

            $collection = $column->getCollection();

            if (! is_string($collection)) {
                $collection = 'default';
            }

            foreach ($records as $record) {
                $url = $record->getFallbackMediaUrl($collection, $column->getConversion() ?? '');

                if (blank($url)) {
                    continue;
                }

                return $url;
            }

            return null;
        });
    }

    public function collection(string | AllMediaCollections | Closure | null $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function allCollections(): static
    {
        $this->collection(AllMediaCollections::make());

        return $this;
    }

    public function conversion(string | Closure | null $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }

    public function getCollection(): string | AllMediaCollections | null
    {
        return $this->evaluate($this->collection);
    }

    public function getConversion(): ?string
    {
        return $this->evaluate($this->conversion);
    }

    public function getImageUrl(?string $state = null): ?string
    {
        $record = $this->getRecord();

        if ($this->hasRelationship($record)) {
            $record = $this->getRelationshipResults($record);
        }

        $records = Arr::wrap($record);

        foreach ($records as $record) {
            /** @var Model $record */

            /** @var ?Media $media */
            $media = $record->getRelationValue('media')->first(fn (Media $media): bool => $media->uuid === $state);

            if (! $media) {
                continue;
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

        return null;
    }

    /**
     * @return array<string>
     */
    public function getState(): array
    {
        $record = $this->getRecord();

        if ($this->hasRelationship($record)) {
            $record = $this->getRelationshipResults($record);
        }

        $records = Arr::wrap($record);

        $state = [];

        $collection = $this->getCollection() ?? 'default';

        foreach ($records as $record) {
            /** @var Model $record */
            $state = [
                ...$state,
                ...$record->getRelationValue('media')
                    ->when(
                        ! $collection instanceof AllMediaCollections,
                        fn (MediaCollection $mediaCollection) => $mediaCollection->filter(fn (Media $media): bool => $media->getAttributeValue('collection_name') === $collection),
                    )
                    ->when(
                        $this->hasMediaFilter(),
                        fn (Collection $media) => $this->filterMedia($media)
                    )
                    ->sortBy('order_column')
                    ->pluck('uuid')
                    ->all(),
            ];
        }

        return array_unique($state);
    }

    public function applyEagerLoading(Builder | Relation $query): Builder | Relation
    {
        if ($this->isHidden()) {
            return $query;
        }

        /** @phpstan-ignore-next-line */
        $modifyMediaQuery = fn (Builder | Relation $query) => $query->ordered();

        if ($this->hasRelationship($query->getModel())) {
            return $query->with([
                "{$this->getRelationshipName()}.media" => $modifyMediaQuery,
            ]);
        }

        return $query->with(['media' => $modifyMediaQuery]);
    }
}
