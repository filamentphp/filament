<?php

use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Tests\Fixtures\Models\MediaPost;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

uses(TestCase::class);

it('implements `FileAttachmentProvider`', function (): void {
    $provider = SpatieMediaLibraryFileAttachmentProvider::make();

    expect($provider)->toBeInstanceOf(FileAttachmentProvider::class);
});

describe('collection', function (): void {
    it('defaults to attribute name when no collection is set', function (): void {
        $attribute = RichContentAttribute::make(new User, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        expect($provider->getCollection())->toBe('content');
    });

    it('can set `collection()`', function (): void {
        $attribute = RichContentAttribute::make(new User, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute)
            ->collection('attachments');

        expect($provider->getCollection())->toBe('attachments');
    });

    it('can clear `collection()` with `null` to fall back to attribute name', function (): void {
        $attribute = RichContentAttribute::make(new User, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute)
            ->collection('attachments')
            ->collection(null);

        expect($provider->getCollection())->toBe('content');
    });
});

describe('preserve filenames', function (): void {
    it('defaults `shouldPreserveFilenames()` to `false`', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make();

        expect($provider->shouldPreserveFilenames())->toBeFalse();
    });

    it('can set `preserveFilenames()`', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->preserveFilenames();

        expect($provider->shouldPreserveFilenames())->toBeTrue();
    });

    it('can set `preserveFilenames()` to `false`', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->preserveFilenames()
            ->preserveFilenames(false);

        expect($provider->shouldPreserveFilenames())->toBeFalse();
    });

    it('can set `preserveFilenames()` with a `Closure`', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->preserveFilenames(static fn (): bool => true);

        expect($provider->shouldPreserveFilenames())->toBeTrue();
    });
});

describe('custom properties', function (): void {
    it('returns empty array for `getCustomProperties()` by default', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make();

        expect($provider->getCustomProperties())->toBe([]);
    });

    it('can set `customProperties()` with an array', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->customProperties(['key' => 'value']);

        expect($provider->getCustomProperties())->toBe(['key' => 'value']);
    });

    it('can set `customProperties()` with a `Closure`', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->customProperties(static fn (): array => ['dynamic' => true]);

        expect($provider->getCustomProperties())->toBe(['dynamic' => true]);
    });

    it('can clear `customProperties()` with `null`', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->customProperties(['key' => 'value'])
            ->customProperties(null);

        expect($provider->getCustomProperties())->toBe([]);
    });
});

describe('media name', function (): void {
    it('returns `null` for `getMediaName()` by default', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make();
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($provider->getMediaName($file))->toBeNull();
    });

    it('can set `mediaName()` with a string', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->mediaName('custom-name');
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($provider->getMediaName($file))->toBe('custom-name');
    });

    it('can clear `mediaName()` with `null`', function (): void {
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->mediaName('custom-name')
            ->mediaName(null);
        $file = Mockery::mock(TemporaryUploadedFile::class);

        expect($provider->getMediaName($file))->toBeNull();
    });
});

it('returns `private` for `getDefaultFileAttachmentVisibility()`', function (): void {
    $provider = SpatieMediaLibraryFileAttachmentProvider::make();

    expect($provider->getDefaultFileAttachmentVisibility())->toBe('private');
});

it('returns `true` for `isExistingRecordRequiredToSaveNewFileAttachments()`', function (): void {
    $provider = SpatieMediaLibraryFileAttachmentProvider::make();

    expect($provider->isExistingRecordRequiredToSaveNewFileAttachments())->toBeTrue();
});

it('returns fluent `$this` from `attribute()`', function (): void {
    $provider = SpatieMediaLibraryFileAttachmentProvider::make();
    $attribute = RichContentAttribute::make(new User, 'content');

    expect($provider->attribute($attribute))->toBe($provider);
});

describe('existing model', function (): void {
    it('returns `null` from `getExistingModel()` when model does not exist in DB', function (): void {
        $attribute = RichContentAttribute::make(new MediaPost, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        expect($provider->getExistingModel())->toBeNull();
    });

    it('returns the model from `getExistingModel()` when model exists and implements `HasMedia`', function (): void {
        $record = MediaPost::factory()->create();
        $attribute = RichContentAttribute::make($record, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        expect($provider->getExistingModel())->toBe($record);
    });

    it('throws `LogicException` from `getExistingModel()` when model exists but does not implement `HasMedia`', function (): void {
        $user = User::factory()->create();
        $attribute = RichContentAttribute::make($user, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        expect(static fn () => $provider->getExistingModel())
            ->toThrow(LogicException::class);
    });

    it('returns `null` from `getMedia()` when model does not exist', function (): void {
        $attribute = RichContentAttribute::make(new MediaPost, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        expect($provider->getMedia())->toBeNull();
    });

    it('returns `null` from `getFileAttachmentUrl()` when media is null', function (): void {
        $attribute = RichContentAttribute::make(new MediaPost, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        expect($provider->getFileAttachmentUrl('some-uuid'))->toBeNull();
    });
});

describe('media operations', function (): void {
    beforeEach(function (): void {
        Storage::fake('public');
    });

    it('can retrieve media from an existing record', function (): void {
        $record = MediaPost::factory()->create();

        $record->addMediaFromString('test-content')
            ->usingFileName('attachment.jpg')
            ->toMediaCollection('content');

        $attribute = RichContentAttribute::make($record, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        $media = $provider->getMedia();

        expect($media)->toHaveCount(1);
    });

    it('can get a file attachment URL for existing media', function (): void {
        $record = MediaPost::factory()->create();

        $mediaItem = $record->addMediaFromString('test-content')
            ->usingFileName('attachment.jpg')
            ->toMediaCollection('content');

        $attribute = RichContentAttribute::make($record->fresh(), 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        // Force media loading
        $provider->getMedia();

        $url = $provider->getFileAttachmentUrl($mediaItem->uuid);

        expect($url)->toBeString();
        expect($url)->toContain('attachment.jpg');
    });

    it('returns `null` from `getFileAttachmentUrl()` for a non-existent UUID', function (): void {
        $record = MediaPost::factory()->create();

        $record->addMediaFromString('test-content')
            ->usingFileName('attachment.jpg')
            ->toMediaCollection('content');

        $attribute = RichContentAttribute::make($record->fresh(), 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        $provider->getMedia();

        $url = $provider->getFileAttachmentUrl('non-existent-uuid');

        expect($url)->toBeNull();
    });

    it('can save an uploaded file attachment', function (): void {
        $record = MediaPost::factory()->create();

        $attribute = RichContentAttribute::make($record, 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'file-content');

        $file = Mockery::mock(TemporaryUploadedFile::class);
        $file->shouldReceive('exists')->andReturn(true);
        $file->shouldReceive('get')->andReturn('file-content');
        $file->shouldReceive('getClientOriginalName')->andReturn('document.pdf');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('pdf');
        $file->shouldReceive('getMimeType')->andReturn('application/pdf');

        $uuid = $provider->saveUploadedFileAttachment($file);

        expect($uuid)->toBeString();
        expect($record->getMedia('content'))->toHaveCount(1);
        expect($record->getMedia('content')->first()->uuid)->toBe($uuid);

        @unlink($tempFile);
    });

    it('can clean up file attachments except specified UUIDs', function (): void {
        $record = MediaPost::factory()->create();

        $media1 = $record->addMediaFromString('first')
            ->usingFileName('first.jpg')
            ->toMediaCollection('content');

        $media2 = $record->addMediaFromString('second')
            ->usingFileName('second.jpg')
            ->toMediaCollection('content');

        $media3 = $record->addMediaFromString('third')
            ->usingFileName('third.jpg')
            ->toMediaCollection('content');

        $attribute = RichContentAttribute::make($record->fresh(), 'content');
        $provider = SpatieMediaLibraryFileAttachmentProvider::make()
            ->attribute($attribute);

        // Keep only media2
        $provider->cleanUpFileAttachments([$media2->uuid]);

        $record->refresh();

        expect($record->getMedia('content'))->toHaveCount(1);
        expect($record->getMedia('content')->first()->uuid)->toBe($media2->uuid);
    });
});
