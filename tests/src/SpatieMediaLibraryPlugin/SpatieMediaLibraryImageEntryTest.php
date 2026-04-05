<?php

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Schemas\Schema;
use Filament\SpatieLaravelMediaLibraryPlugin\Collections\AllMediaCollections;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\MediaPost;
use Filament\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

uses(TestCase::class);

describe('collection', function (): void {
    it('returns `null` for `getCollection()` by default', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media');

        expect($entry->getCollection())->toBeNull();
    });

    it('can set `collection()` with a string', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->collection('avatars');

        expect($entry->getCollection())->toBe('avatars');
    });

    it('can set `collection()` with a `Closure`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->collection(static fn (): string => 'dynamic');

        expect($entry->getCollection())->toBe('dynamic');
    });

    it('can clear `collection()` with `null`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->collection('avatars')
            ->collection(null);

        expect($entry->getCollection())->toBeNull();
    });

    it('can set `allCollections()` to use `AllMediaCollections`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->allCollections();

        expect($entry->getCollection())->toBeInstanceOf(AllMediaCollections::class);
    });
});

describe('conversion', function (): void {
    it('returns `null` for `getConversion()` by default', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media');

        expect($entry->getConversion())->toBeNull();
    });

    it('can set `conversion()`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->conversion('thumb');

        expect($entry->getConversion())->toBe('thumb');
    });

    it('can set `conversion()` with a `Closure`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->conversion(static fn (): string => 'preview');

        expect($entry->getConversion())->toBe('preview');
    });

    it('can clear `conversion()` with `null`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->conversion('thumb')
            ->conversion(null);

        expect($entry->getConversion())->toBeNull();
    });
});

describe('media filter', function (): void {
    it('defaults `hasMediaFilter()` to `false`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media');

        expect($entry->hasMediaFilter())->toBeFalse();
    });

    it('can set `filterMediaUsing()`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->filterMediaUsing(static fn (Collection $media): Collection => $media);

        expect($entry->hasMediaFilter())->toBeTrue();
    });

    it('can clear `filterMediaUsing()` with `null`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->filterMediaUsing(static fn (Collection $media): Collection => $media)
            ->filterMediaUsing(null);

        expect($entry->hasMediaFilter())->toBeFalse();
    });

    it('can filter a media collection with `filterMedia()`', function (): void {
        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->filterMediaUsing(static fn (Collection $media): Collection => $media->filter(
                static fn ($item): bool => $item['type'] === 'image'
            ));

        $media = new Collection([
            ['type' => 'image', 'uuid' => 'a'],
            ['type' => 'document', 'uuid' => 'b'],
            ['type' => 'image', 'uuid' => 'c'],
        ]);

        $filtered = $entry->filterMedia($media);

        expect($filtered)->toHaveCount(2);
        expect($filtered->pluck('uuid')->values()->all())->toBe(['a', 'c']);
    });
});

describe('state from media', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
    });

    it('can retrieve UUIDs from the media collection as state', function (): void {
        $record = MediaPost::factory()->create();

        $media1 = $record->addMediaFromString('first')
            ->usingFileName('first.jpg')
            ->toMediaCollection('avatars');

        $media2 = $record->addMediaFromString('second')
            ->usingFileName('second.jpg')
            ->toMediaCollection('avatars');

        $record->load('media');

        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->collection('avatars')
            ->container(
                Schema::make(Livewire::make())
                    ->record($record)
            );

        $state = $entry->getState();

        expect($state)->toContain($media1->uuid);
        expect($state)->toContain($media2->uuid);
        expect($state)->toHaveCount(2);
    });

    it('only returns UUIDs from the specified collection', function (): void {
        $record = MediaPost::factory()->create();

        $avatarMedia = $record->addMediaFromString('avatar')
            ->usingFileName('avatar.jpg')
            ->toMediaCollection('avatars');

        $record->addMediaFromString('document')
            ->usingFileName('doc.pdf')
            ->toMediaCollection('documents');

        $record->load('media');

        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->collection('avatars')
            ->container(
                Schema::make(Livewire::make())
                    ->record($record)
            );

        $state = $entry->getState();

        expect($state)->toHaveCount(1);
        expect($state)->toContain($avatarMedia->uuid);
    });

    it('returns UUIDs from all collections when `allCollections()` is used', function (): void {
        $record = MediaPost::factory()->create();

        $record->addMediaFromString('avatar')
            ->usingFileName('avatar.jpg')
            ->toMediaCollection('avatars');

        $record->addMediaFromString('document')
            ->usingFileName('doc.pdf')
            ->toMediaCollection('documents');

        $record->load('media');

        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->allCollections()
            ->container(
                Schema::make(Livewire::make())
                    ->record($record)
            );

        $state = $entry->getState();

        expect($state)->toHaveCount(2);
    });

    it('returns empty array when record has no media', function (): void {
        $record = MediaPost::factory()->create();
        $record->load('media');

        $entry = SpatieMediaLibraryImageEntry::make('media')
            ->collection('avatars')
            ->container(
                Schema::make(Livewire::make())
                    ->record($record)
            );

        $state = $entry->getState();

        expect($state)->toBe([]);
    });
});
