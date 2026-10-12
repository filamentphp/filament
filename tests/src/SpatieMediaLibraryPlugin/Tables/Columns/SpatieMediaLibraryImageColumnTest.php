<?php

use Filament\SpatieLaravelMediaLibraryPlugin\Collections\AllMediaCollections;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tests\Fixtures\Livewire\SpatieMediaLibraryTableForm;
use Filament\Tests\Fixtures\Models\MediaPost;
use Filament\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('collection', function (): void {
    it('returns `null` for `getCollection()` by default', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media');

        expect($column->getCollection())->toBeNull();
    });

    it('can set `collection()` with a string', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->collection('avatars');

        expect($column->getCollection())->toBe('avatars');
    });

    it('can set `collection()` with a `Closure`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->collection(static fn (): string => 'dynamic');

        expect($column->getCollection())->toBe('dynamic');
    });

    it('can clear `collection()` with `null`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->collection('avatars')
            ->collection(null);

        expect($column->getCollection())->toBeNull();
    });

    it('can set `allCollections()` to use `AllMediaCollections`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->allCollections();

        expect($column->getCollection())->toBeInstanceOf(AllMediaCollections::class);
    });
});

describe('conversion', function (): void {
    it('returns `null` for `getConversion()` by default', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media');

        expect($column->getConversion())->toBeNull();
    });

    it('can set `conversion()`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->conversion('thumb');

        expect($column->getConversion())->toBe('thumb');
    });

    it('can set `conversion()` with a `Closure`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->conversion(static fn (): string => 'preview');

        expect($column->getConversion())->toBe('preview');
    });

    it('can clear `conversion()` with `null`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->conversion('thumb')
            ->conversion(null);

        expect($column->getConversion())->toBeNull();
    });
});

describe('media filter', function (): void {
    it('defaults `hasMediaFilter()` to `false`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media');

        expect($column->hasMediaFilter())->toBeFalse();
    });

    it('can set `filterMediaUsing()`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->filterMediaUsing(static fn (Collection $media): Collection => $media);

        expect($column->hasMediaFilter())->toBeTrue();
    });

    it('can clear `filterMediaUsing()` with `null`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->filterMediaUsing(static fn (Collection $media): Collection => $media)
            ->filterMediaUsing(null);

        expect($column->hasMediaFilter())->toBeFalse();
    });

    it('can filter a media collection with `filterMedia()`', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->filterMediaUsing(static fn (Collection $media): Collection => $media->filter(
                static fn ($item): bool => $item['type'] === 'image'
            ));

        $media = new Collection([
            ['type' => 'image', 'uuid' => 'a'],
            ['type' => 'document', 'uuid' => 'b'],
            ['type' => 'image', 'uuid' => 'c'],
        ]);

        $filtered = $column->filterMedia($media);

        expect($filtered)->toHaveCount(2);
        expect($filtered->pluck('uuid')->values()->all())->toBe(['a', 'c']);
    });
});

describe('eager loading', function (): void {
    it('can apply eager loading to a query', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->collection('avatars');

        $query = MediaPost::query();
        $result = $column->applyEagerLoading($query);

        $eagerLoads = $result->getEagerLoads();

        expect($eagerLoads)->toHaveKey('media');
    });

    it('does not apply eager loading when column is hidden', function (): void {
        $column = SpatieMediaLibraryImageColumn::make('media')
            ->collection('avatars')
            ->hidden();

        $query = MediaPost::query();
        $result = $column->applyEagerLoading($query);

        $eagerLoads = $result->getEagerLoads();

        expect($eagerLoads)->not->toHaveKey('media');
    });
});

describe('state from media', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
    });

    it('can render column for a record with media', function (): void {
        $record = MediaPost::factory()->create();

        $record->addMediaFromString('first')
            ->usingFileName('first.jpg')
            ->toMediaCollection('avatars');

        livewire(SpatieMediaLibraryTableForm::class)
            ->assertTableColumnExists('media')
            ->assertCanRenderTableColumn('media');
    });

    it('injects the media model while rendering each item', function (): void {
        $record = MediaPost::factory()->create();

        $media = $record->addMediaFromString('first')
            ->usingFileName('first.jpg')
            ->toMediaCollection('avatars');

        $column = livewire(SpatieMediaLibraryTableForm::class)
            ->instance()
            ->getTable()
            ->getColumn('media')
            ->record($record)
            ->recordKey((string) $record->getKey())
            ->conversion(static fn (string $state, Media $relatedRecord): string => $state === $relatedRecord->uuid ? '' : 'missing')
            ->alt(static fn (string $state, Media $relatedRecord): string => "media-{$relatedRecord->getKey()}-{$state}");

        $column->clearCachedState();

        expect($column->toEmbeddedHtml())->toContain('alt="media-' . $media->getKey() . '-' . $media->uuid . '"');
    });

    it('can render column for a record without media', function (): void {
        MediaPost::factory()->create();

        livewire(SpatieMediaLibraryTableForm::class)
            ->assertTableColumnExists('media')
            ->assertCanRenderTableColumn('media');
    });
});
