<?php

use Filament\Tests\Fixtures\Livewire\RichEditorFileAttachmentForm;
use Filament\Tests\Fixtures\Models\MediaPostWithRichContent;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

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

    it('rejects content referencing another record\'s media by default', function () use ($makeTipTapDocument, $makeImage, $makeParagraph): void {
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
