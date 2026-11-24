<?php

use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Livewire\RichEditorFileAttachmentForm;
use Filament\Tests\Fixtures\Models\MediaPostWithRichContent;
use Filament\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Models\Observers\MediaObserver;
use Spatie\MediaLibrary\Support\FileRemover\DefaultFileRemover;

use function Filament\Tests\livewire;

uses(TestCase::class);

$makeTipTapDocument = static function (array $content = []): array {
    return [
        'type' => 'doc',
        'content' => $content,
    ];
};

$makeImage = static function (string $id, ?string $source = null): array {
    return [
        'type' => 'image',
        'attrs' => [
            'id' => $id,
            'src' => $source,
        ],
    ];
};

$makeParagraph = static function (string $text): array {
    return [
        'type' => 'paragraph',
        'content' => [
            ['type' => 'text', 'text' => $text],
        ],
    ];
};

beforeEach(function (): void {
    Storage::fake('tmp-for-tests');
    Storage::fake('public');
});

describe('provider authorization', function () use ($makeTipTapDocument, $makeImage): void {
    it('does not resolve rejected IDs as disk paths in the field or renderer', function (?string $source) use ($makeTipTapDocument, $makeImage): void {
        $storage = Storage::fake('local');
        $path = 'outside-provider/harmless.txt';
        $storage->put($path, 'Not a provider attachment');
        $record = MediaPostWithRichContent::create(['title' => 'Original']);
        $editor = RichEditor::make('content')
            ->fileAttachmentsDisk('local')
            ->fileAttachmentsVisibility('private');
        $schema = Schema::make(Livewire::make())
            ->model($record)
            ->statePath('data')
            ->components([$editor]);
        $schema->getComponents();
        $provider = $editor->getFileAttachmentProvider();
        $renderer = $record->getRichContentAttribute('content')->getRenderer();

        expect($storage->exists($path))->toBeTrue()
            ->and($provider->getFileAttachmentUrl($path))->toBeNull();

        Storage::partialMock()->shouldNotReceive('disk');

        expect($editor->getFileAttachmentUrl($path))->toBeNull()
            ->and($renderer->getFileAttachmentUrl($path))->toBeNull();

        $schema->fill(['content' => $makeTipTapDocument([$makeImage($path, $source)])]);

        expect(data_get($editor->getRawState(), 'content.0.attrs.src'))->toBeNull();
    })->with([null, 'https://existing.test/harmless.txt']);

    it('resolves provider-owned IDs in the field, renderer, and hydrated content', function () use ($makeTipTapDocument, $makeImage): void {
        $record = MediaPostWithRichContent::create(['title' => 'Original']);
        $media = $record->addMediaFromString('Owned attachment')->usingFileName('owned.txt')->toMediaCollection('content');
        $record->getRichContentAttribute('content')->fileAttachmentsVisibility('public');
        $editor = RichEditor::make('content');
        Schema::make(Livewire::make())
            ->model($record)
            ->statePath('data')
            ->components([$editor])
            ->fill(['content' => $makeTipTapDocument([$makeImage($media->uuid, 'https://existing.test/stale.txt')])]);

        expect($editor->getFileAttachmentProvider()->getFileAttachmentUrl($media->uuid))->toBe($media->getUrl())
            ->and($editor->getFileAttachmentUrl($media->uuid))->toBe($media->getUrl())
            ->and($record->getRichContentAttribute('content')->getRenderer()->getFileAttachmentUrl($media->uuid))->toBe($media->getUrl())
            ->and(data_get($editor->getRawState(), 'content.0.attrs.src'))->toBe($media->getUrl());
    });

    it('keeps `getFileAttachmentUrlUsing()` authoritative over providers and disks', function (?string $url, bool $isProviderOwned): void {
        $storage = Storage::fake('local');
        $record = MediaPostWithRichContent::create(['title' => 'Original']);
        $media = $record->addMediaFromString('Owned attachment')->usingFileName('owned.txt')->toMediaCollection('content');
        $id = $isProviderOwned ? $media->uuid : 'outside-provider/harmless.txt';
        $storage->put($id, 'Disk fallback must not be used');
        $editor = RichEditor::make('content')
            ->fileAttachmentsDisk('local')
            ->fileAttachmentsVisibility('private')
            ->getFileAttachmentUrlUsing(static fn (string $file): ?string => $url === null ? null : $url . $file)
            ->container(Schema::make(Livewire::make())->model($record));

        Storage::partialMock()->shouldNotReceive('disk');

        expect($editor->getFileAttachmentUrl($id))->toBe($url === null ? null : $url . $id);
    })->with([null, 'https://custom.test/'])->with([true, false]);

    it('preserves authorized cross-record copies through saving while cleaning up unused media', function (bool $shouldCopy) use ($makeTipTapDocument, $makeImage): void {
        Media::observe(app(MediaObserver::class));
        config()->set('media-library.file_remover_class', DefaultFileRemover::class);

        $storage = Storage::fake('local');
        $path = 'outside-provider/harmless.txt';
        $storage->put($path, 'Not a provider attachment');
        $record = MediaPostWithRichContent::create(['title' => 'Original'])->fresh();
        $unusedMedia = $record->addMediaFromString('Unused attachment')->usingFileName('unused.txt')->toMediaCollection('content');
        $record->getRichContentAttribute('content')->fileAttachmentsVisibility('public');
        $editor = RichEditor::make('content')
            ->fileAttachmentsDisk('local')
            ->fileAttachmentsVisibility('public');
        $schema = Schema::make(Livewire::make())
            ->model($record)
            ->statePath('data')
            ->components([$editor])
            ->fill(['content' => $makeTipTapDocument([$makeImage($path)])]);

        $temporaryFileName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded(
            UploadedFile::fake()->create('copied.txt'),
        );
        Storage::disk('tmp-for-tests')->put("livewire-tmp/{$temporaryFileName}", 'Explicitly authorized copy');
        $temporaryFile = TemporaryUploadedFile::createFromLivewire($temporaryFileName);
        $provider = $editor->getFileAttachmentProvider();
        $editor->saveFileAttachmentFromAnotherRecordUsing(static function (string $file) use ($shouldCopy, $provider, $temporaryFile, $path, $record): ?string {
            if ((! $shouldCopy) || ($file !== $path)) {
                return null;
            }

            $id = $provider->saveUploadedFileAttachment($temporaryFile);

            // Include the newly copied media in the collection inspected by cleanup.
            $record->load('media');

            return $id;
        });

        $record->update($schema->getState());
        $media = $record->fresh()->getMedia('content');

        expect($media)->toHaveCount($shouldCopy ? 1 : 0)
            ->and($editor->getFileAttachmentProvider()->getFileAttachmentUrl($path))->toBeNull()
            ->and($editor->getFileAttachmentUrl($path))->toBeNull();
        Storage::disk('public')->assertMissing($unusedMedia->getPathRelativeToRoot());

        if ($shouldCopy) {
            Storage::disk('public')->assertExists($media->first()->getPathRelativeToRoot());
            $copiedMediaUuid = data_get($editor->getRawState(), 'content.0.attrs.id');

            expect(strtolower($copiedMediaUuid))->toBe(strtolower($media->first()->uuid))
                ->and(data_get($editor->getRawState(), 'content.0.attrs.src'))->toBe($media->first()->getUrl())
                ->and($record->fresh()->content)->toContain('data-id="' . $copiedMediaUuid . '"', 'src="' . $media->first()->getUrl() . '"');
        } else {
            // Unresolved IDs remain in the cleanup exclusion list, not the provider's collection.
            expect($editor->resolveFileAttachmentIds())->toBe([$path])
                ->and(data_get($editor->getRawState(), 'content.0.attrs.id'))->toBe($path)
                ->and(data_get($editor->getRawState(), 'content.0.attrs.src'))->toBeNull();
        }
    })->with([false, true]);
});

describe('create', function () use ($makeTipTapDocument, $makeParagraph, $makeImage): void {
    it('creates a record with a file attachment as Spatie media', function () use ($makeTipTapDocument, $makeParagraph, $makeImage): void {
        livewire(RichEditorFileAttachmentForm::class)
            ->call('createWithAttachments', ['temp-upload-1'], [
                'title' => 'Post With Image',
                'content' => $makeTipTapDocument([
                    $makeParagraph('Hello world'),
                    $makeImage('temp-upload-1', 'blob:temporary'),
                ]),
            ]);

        $record = MediaPostWithRichContent::first();

        expect($record)->not->toBeNull();
        expect($record->title)->toBe('Post With Image');
        expect($record->getMedia('content'))->toHaveCount(1);
    });

    it('stores the media UUID in the saved content', function () use ($makeTipTapDocument, $makeParagraph, $makeImage): void {
        livewire(RichEditorFileAttachmentForm::class)
            ->call('createWithAttachments', ['temp-upload-1'], [
                'title' => 'Post With Image',
                'content' => $makeTipTapDocument([
                    $makeParagraph('Before image'),
                    $makeImage('temp-upload-1', 'blob:temporary'),
                ]),
            ]);

        $record = MediaPostWithRichContent::first();
        $media = $record->getMedia('content')->first();

        expect(strtolower($record->content))->toContain(strtolower($media->uuid));
    });

    it('creates multiple media from multiple file attachments', function () use ($makeTipTapDocument, $makeImage, $makeParagraph): void {
        livewire(RichEditorFileAttachmentForm::class)
            ->call('createWithAttachments', ['temp-1', 'temp-2', 'temp-3'], [
                'title' => 'Post With Multiple Images',
                'content' => $makeTipTapDocument([
                    $makeImage('temp-1', 'blob:temp'),
                    $makeParagraph('Between images'),
                    $makeImage('temp-2', 'blob:temp'),
                    $makeImage('temp-3', 'blob:temp'),
                ]),
            ]);

        $record = MediaPostWithRichContent::first();

        expect($record->getMedia('content'))->toHaveCount(3);
    });

    it('creates a record with text-only content and no media', function () use ($makeTipTapDocument, $makeParagraph): void {
        livewire(RichEditorFileAttachmentForm::class)
            ->call('createWithAttachments', [], [
                'title' => 'Text Only Post',
                'content' => $makeTipTapDocument([
                    $makeParagraph('Just text, no images'),
                ]),
            ]);

        $record = MediaPostWithRichContent::first();

        expect($record)->not->toBeNull();
        expect($record->getMedia('content'))->toHaveCount(0);
    });
});

describe('update', function () use ($makeTipTapDocument, $makeParagraph, $makeImage): void {
    it('only cleans up attachments during `getState()` when the editor and its containing schemas are enabled', function (bool $isSchemaDisabled, bool $isParentDisabled, bool $isFieldDisabled) use ($makeTipTapDocument, $makeImage): void {
        // The test case does not boot Spatie's provider, which normally configures file deletion.
        Media::observe(app(MediaObserver::class));
        config()->set('media-library.file_remover_class', DefaultFileRemover::class);

        $record = MediaPostWithRichContent::create(['title' => 'Original'])->fresh();
        $keptMedia = $record->addMediaFromString('Kept attachment')->usingFileName('kept.txt')->toMediaCollection('content');
        $removedMedia = $record->addMediaFromString('Removed attachment')->usingFileName('removed.txt')->toMediaCollection('content');
        $editor = RichEditor::make('content')->disabled($isFieldDisabled)->saved();
        $schema = Schema::make(Livewire::make())
            ->model($record)
            ->statePath('data')
            ->disabled($isSchemaDisabled)
            ->components([
                Group::make([$editor])->disabled($isParentDisabled)->saved(),
            ])
            ->fill([
                'content' => $makeTipTapDocument([$makeImage($keptMedia->uuid)]),
            ]);

        $schema->getState();
        $shouldCleanUp = (! $isSchemaDisabled) && (! $isParentDisabled) && (! $isFieldDisabled);

        expect($record->fresh()->getMedia('content'))->toHaveCount($shouldCleanUp ? 1 : 2)
            ->and(Storage::disk('public')->exists($removedMedia->getPathRelativeToRoot()))->toBe(! $shouldCleanUp);

        if (! $shouldCleanUp) {
            $editor->saveFileAttachments();
        }

        expect($record->fresh()->getMedia('content')->modelKeys())->toBe([$keptMedia->getKey()]);
        Storage::disk('public')->assertExists($keptMedia->getPathRelativeToRoot());
        Storage::disk('public')->assertMissing($removedMedia->getPathRelativeToRoot());
    })->with([
        'enabled' => [false, false, false],
        'disabled schema' => [true, false, false],
        'disabled parent' => [false, true, false],
        'disabled editor' => [false, false, true],
    ]);

    it('adds new media when adding an image to existing content', function () use ($makeTipTapDocument, $makeParagraph, $makeImage): void {
        $record = MediaPostWithRichContent::create([
            'title' => 'Original',
            'content' => json_encode($makeTipTapDocument([
                $makeParagraph('Original text'),
            ])),
        ]);

        livewire(RichEditorFileAttachmentForm::class, ['recordId' => $record->id])
            ->call('saveWithAttachments', ['new-image-1'], [
                'title' => 'Updated',
                'content' => $makeTipTapDocument([
                    $makeParagraph('Updated text'),
                    $makeImage('new-image-1', 'blob:temporary'),
                ]),
            ]);

        $record->refresh();

        expect($record->title)->toBe('Updated');
        expect($record->getMedia('content'))->toHaveCount(1);
    });

    it('removes orphaned media when removing an image from content', function () use ($makeTipTapDocument, $makeParagraph, $makeImage): void {
        $record = MediaPostWithRichContent::create(['title' => 'Original']);

        $media = $record
            ->addMediaFromString('existing image content')
            ->usingFileName('existing.jpg')
            ->toMediaCollection('content');

        $record->update([
            'content' => json_encode($makeTipTapDocument([
                $makeParagraph('Text with image'),
                $makeImage($media->uuid, $media->getUrl()),
            ])),
        ]);

        livewire(RichEditorFileAttachmentForm::class, ['recordId' => $record->id])
            ->call('saveWithAttachments', [], [
                'title' => 'Updated',
                'content' => $makeTipTapDocument([
                    $makeParagraph('Text without image'),
                ]),
            ]);

        $record->refresh();

        expect($record->getMedia('content'))->toHaveCount(0);
    });

    it('keeps existing media and adds new media when editing content', function () use ($makeTipTapDocument, $makeImage): void {
        $record = MediaPostWithRichContent::create(['title' => 'Original']);

        $existingMedia = $record
            ->addMediaFromString('existing image content')
            ->usingFileName('existing.jpg')
            ->toMediaCollection('content');

        $record->update([
            'content' => json_encode($makeTipTapDocument([
                $makeImage($existingMedia->uuid, $existingMedia->getUrl()),
            ])),
        ]);

        livewire(RichEditorFileAttachmentForm::class, ['recordId' => $record->id])
            ->call('saveWithAttachments', ['new-upload'], [
                'title' => 'Updated',
                'content' => $makeTipTapDocument([
                    $makeImage($existingMedia->uuid, $existingMedia->getUrl()),
                    $makeImage('new-upload', 'blob:temporary'),
                ]),
            ]);

        $record->refresh();

        expect($record->getMedia('content'))->toHaveCount(2);

        $mediaUuids = $record->getMedia('content')->pluck('uuid')->all();
        expect($mediaUuids)->toContain($existingMedia->fresh()->uuid);
    });

    it('replaces media when swapping one image for another', function () use ($makeTipTapDocument, $makeImage): void {
        $record = MediaPostWithRichContent::create(['title' => 'Original']);

        $oldMedia = $record
            ->addMediaFromString('old image')
            ->usingFileName('old.jpg')
            ->toMediaCollection('content');

        $record->update([
            'content' => json_encode($makeTipTapDocument([
                $makeImage($oldMedia->uuid, $oldMedia->getUrl()),
            ])),
        ]);

        livewire(RichEditorFileAttachmentForm::class, ['recordId' => $record->id])
            ->call('saveWithAttachments', ['replacement'], [
                'title' => 'Updated',
                'content' => $makeTipTapDocument([
                    $makeImage('replacement', 'blob:temporary'),
                ]),
            ]);

        $record->refresh();

        expect($record->getMedia('content'))->toHaveCount(1);
        expect(strtolower($record->getMedia('content')->first()->uuid))->not->toBe(strtolower($oldMedia->uuid));
    });

    it('rejects content referencing another record\'s media when `preventFileAttachmentPathTampering()` is enabled', function () use ($makeTipTapDocument, $makeImage, $makeParagraph): void {
        RichEditor::configureUsing(fn (RichEditor $component): RichEditor => $component->preventFileAttachmentPathTampering());

        $otherRecord = MediaPostWithRichContent::create(['title' => 'Other']);

        $otherMedia = $otherRecord
            ->addMediaFromString('other record image')
            ->usingFileName('other.jpg')
            ->toMediaCollection('content');

        $otherRecord->update([
            'content' => json_encode($makeTipTapDocument([
                $makeImage($otherMedia->uuid, $otherMedia->getUrl()),
            ])),
        ]);

        $record = MediaPostWithRichContent::create([
            'title' => 'Original',
            'content' => json_encode($makeTipTapDocument([
                $makeParagraph('No images here'),
            ])),
        ]);

        livewire(RichEditorFileAttachmentForm::class, ['recordId' => $record->id])
            ->call('saveWithAttachments', [], [
                'title' => 'Updated',
                'content' => $makeTipTapDocument([
                    $makeImage($otherMedia->uuid, $otherMedia->getUrl()),
                ]),
            ])
            ->assertHasFormErrors(['content']);

        // The other record keeps its media, and this record is not given any.
        expect($otherRecord->fresh()->getMedia('content'))->toHaveCount(1);
        expect($record->fresh()->getMedia('content'))->toHaveCount(0);
    });
});
