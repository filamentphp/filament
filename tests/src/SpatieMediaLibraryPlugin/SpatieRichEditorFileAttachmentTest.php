<?php

use Filament\Tests\Fixtures\Livewire\RichEditorFileAttachmentForm;
use Filament\Tests\Fixtures\Models\MediaPostWithRichContent;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Storage::fake('tmp-for-tests');
    Storage::fake('public');
});

function makeTipTapDoc(array $content = []): array
{
    return [
        'type' => 'doc',
        'content' => $content,
    ];
}

function makeImage(string $id, ?string $src = null): array
{
    return [
        'type' => 'image',
        'attrs' => [
            'id' => $id,
            'src' => $src,
        ],
    ];
}

function makeParagraph(string $text): array
{
    return [
        'type' => 'paragraph',
        'content' => [
            ['type' => 'text', 'text' => $text],
        ],
    ];
}

describe('create', function (): void {
    test('it creates a record with a file attachment as Spatie media', function (): void {
        livewire(RichEditorFileAttachmentForm::class)
            ->call('createWithAttachments', ['temp-upload-1'], [
                'title' => 'Post With Image',
                'content' => makeTipTapDoc([
                    makeParagraph('Hello world'),
                    makeImage('temp-upload-1', 'blob:temporary'),
                ]),
            ]);

        $record = MediaPostWithRichContent::first();

        expect($record)->not->toBeNull();
        expect($record->title)->toBe('Post With Image');
        expect($record->getMedia('content'))->toHaveCount(1);
    });

    test('it stores the media UUID in the saved content', function (): void {
        livewire(RichEditorFileAttachmentForm::class)
            ->call('createWithAttachments', ['temp-upload-1'], [
                'title' => 'Post With Image',
                'content' => makeTipTapDoc([
                    makeParagraph('Before image'),
                    makeImage('temp-upload-1', 'blob:temporary'),
                ]),
            ]);

        $record = MediaPostWithRichContent::first();
        $media = $record->getMedia('content')->first();

        expect($record->content)->toContain($media->uuid);
    });

    test('it creates multiple media from multiple file attachments', function (): void {
        livewire(RichEditorFileAttachmentForm::class)
            ->call('createWithAttachments', ['temp-1', 'temp-2', 'temp-3'], [
                'title' => 'Post With Multiple Images',
                'content' => makeTipTapDoc([
                    makeImage('temp-1', 'blob:temp'),
                    makeParagraph('Between images'),
                    makeImage('temp-2', 'blob:temp'),
                    makeImage('temp-3', 'blob:temp'),
                ]),
            ]);

        $record = MediaPostWithRichContent::first();

        expect($record->getMedia('content'))->toHaveCount(3);
    });

    test('it creates a record with text-only content and no media', function (): void {
        livewire(RichEditorFileAttachmentForm::class)
            ->call('createWithAttachments', [], [
                'title' => 'Text Only Post',
                'content' => makeTipTapDoc([
                    makeParagraph('Just text, no images'),
                ]),
            ]);

        $record = MediaPostWithRichContent::first();

        expect($record)->not->toBeNull();
        expect($record->getMedia('content'))->toHaveCount(0);
    });
});

describe('update', function (): void {
    test('it adds new media when adding an image to existing content', function (): void {
        $record = MediaPostWithRichContent::create([
            'title' => 'Original',
            'content' => json_encode(makeTipTapDoc([
                makeParagraph('Original text'),
            ])),
        ]);

        livewire(RichEditorFileAttachmentForm::class, ['recordId' => $record->id])
            ->call('saveWithAttachments', ['new-image-1'], [
                'title' => 'Updated',
                'content' => makeTipTapDoc([
                    makeParagraph('Updated text'),
                    makeImage('new-image-1', 'blob:temporary'),
                ]),
            ]);

        $record->refresh();

        expect($record->title)->toBe('Updated');
        expect($record->getMedia('content'))->toHaveCount(1);
    });

    test('it removes orphaned media when removing an image from content', function (): void {
        $record = MediaPostWithRichContent::create(['title' => 'Original']);

        $media = $record
            ->addMediaFromString('existing image content')
            ->usingFileName('existing.jpg')
            ->toMediaCollection('content');

        $record->update([
            'content' => json_encode(makeTipTapDoc([
                makeParagraph('Text with image'),
                makeImage($media->uuid, $media->getUrl()),
            ])),
        ]);

        livewire(RichEditorFileAttachmentForm::class, ['recordId' => $record->id])
            ->call('saveWithAttachments', [], [
                'title' => 'Updated',
                'content' => makeTipTapDoc([
                    makeParagraph('Text without image'),
                ]),
            ]);

        $record->refresh();

        expect($record->getMedia('content'))->toHaveCount(0);
    });

    test('it keeps existing media and adds new media when editing content', function (): void {
        $record = MediaPostWithRichContent::create(['title' => 'Original']);

        $existingMedia = $record
            ->addMediaFromString('existing image content')
            ->usingFileName('existing.jpg')
            ->toMediaCollection('content');

        $record->update([
            'content' => json_encode(makeTipTapDoc([
                makeImage($existingMedia->uuid, $existingMedia->getUrl()),
            ])),
        ]);

        livewire(RichEditorFileAttachmentForm::class, ['recordId' => $record->id])
            ->call('saveWithAttachments', ['new-upload'], [
                'title' => 'Updated',
                'content' => makeTipTapDoc([
                    makeImage($existingMedia->uuid, $existingMedia->getUrl()),
                    makeImage('new-upload', 'blob:temporary'),
                ]),
            ]);

        $record->refresh();

        expect($record->getMedia('content'))->toHaveCount(2);

        $mediaUuids = $record->getMedia('content')->pluck('uuid')->all();
        expect($mediaUuids)->toContain($existingMedia->uuid);
    });

    test('it replaces media when swapping one image for another', function (): void {
        $record = MediaPostWithRichContent::create(['title' => 'Original']);

        $oldMedia = $record
            ->addMediaFromString('old image')
            ->usingFileName('old.jpg')
            ->toMediaCollection('content');

        $record->update([
            'content' => json_encode(makeTipTapDoc([
                makeImage($oldMedia->uuid, $oldMedia->getUrl()),
            ])),
        ]);

        livewire(RichEditorFileAttachmentForm::class, ['recordId' => $record->id])
            ->call('saveWithAttachments', ['replacement'], [
                'title' => 'Updated',
                'content' => makeTipTapDoc([
                    makeImage('replacement', 'blob:temporary'),
                ]),
            ]);

        $record->refresh();

        expect($record->getMedia('content'))->toHaveCount(1);
        expect($record->getMedia('content')->first()->uuid)->not->toBe($oldMedia->uuid);
    });
});
