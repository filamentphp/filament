<?php

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\StateCasts\RichEditorStateCast;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;

uses(TestCase::class);

function makeStateCastEditor(): RichEditor
{
    $editor = new RichEditor('content');
    $editor->container(Schema::make(Livewire::make())->statePath('data'));

    return $editor;
}

function walkStateCastResult(array $result, callable $callback): void
{
    $decoded = json_decode(json_encode($result), true);

    $walker = function ($nodes) use (&$walker, $callback): void {
        foreach ($nodes as $node) {
            $callback($node);

            if (! empty($node['content'] ?? null)) {
                $walker($node['content']);
            }
        }
    };

    $walker($decoded['content'] ?? []);
}

describe('`set()`', function (): void {
    it('converts an HTML string to a TipTap document array', function (): void {
        $cast = new RichEditorStateCast(makeStateCastEditor());

        $result = $cast->set('<p>Hello <strong>world</strong></p>');

        expect($result)->toBeArray();
        expect($result['type'] ?? null)->toBe('doc');
        expect($result['content'])->toBeArray()->not->toBeEmpty();
    });

    it('produces an empty paragraph document when the input is `null`', function (): void {
        $cast = new RichEditorStateCast(makeStateCastEditor());

        $result = $cast->set(null);

        expect($result['type'] ?? null)->toBe('doc');
        expect($result['content'])->toBeArray();
    });

    it('accepts `Htmlable` input and converts it via `toHtml()`', function (): void {
        $cast = new RichEditorStateCast(makeStateCastEditor());

        $htmlable = new class implements Htmlable
        {
            public function toHtml(): string
            {
                return '<p>From Htmlable</p>';
            }
        };

        $result = $cast->set($htmlable);

        $foundText = null;

        walkStateCastResult($result, function (array $node) use (&$foundText): void {
            if (($node['type'] ?? null) === 'text') {
                $foundText = $node['text'] ?? null;
            }
        });

        expect($foundText)->toBe('From Htmlable');
    });

    it('resolves image `src` from `data-id` using the editor\'s `getFileAttachmentUrl` callback', function (): void {
        $editor = makeStateCastEditor();

        $editor->getFileAttachmentUrlUsing(static function (string $file): string {
            return "https://cdn.test/{$file}";
        });

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<p><img src="stale" data-id="uploads/avatar.jpg" /></p>');

        $srcs = [];

        walkStateCastResult($result, function (array $node) use (&$srcs): void {
            if (($node['type'] ?? null) === 'image') {
                $srcs[$node['attrs']['id'] ?? ''] = $node['attrs']['src'] ?? null;
            }
        });

        expect($srcs['uploads/avatar.jpg'] ?? null)->toBe('https://cdn.test/uploads/avatar.jpg');
    });

    it('falls back to `getFileAttachmentUrlFromAnotherRecord` when the primary resolver returns `null`', function (): void {
        $editor = makeStateCastEditor();

        $editor->getFileAttachmentUrlUsing(static fn (): ?string => null);
        $editor->getFileAttachmentUrlFromAnotherRecordUsing(static function (string $file): string {
            return "https://other.test/{$file}";
        });

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<p><img src="stale" data-id="uploads/shared.jpg" /></p>');

        $src = null;

        walkStateCastResult($result, function (array $node) use (&$src): void {
            if (($node['type'] ?? null) === 'image') {
                $src = $node['attrs']['src'] ?? null;
            }
        });

        expect($src)->toBe('https://other.test/uploads/shared.jpg');
    });

    it('keeps the existing `src` when both resolvers return `null`', function (): void {
        $editor = makeStateCastEditor();

        $editor->getFileAttachmentUrlUsing(static fn (): ?string => null);
        $editor->getFileAttachmentUrlFromAnotherRecordUsing(static fn (): ?string => null);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<p><img src="https://existing.test/file.jpg" data-id="uploads/file.jpg" /></p>');

        $src = null;

        walkStateCastResult($result, function (array $node) use (&$src): void {
            if (($node['type'] ?? null) === 'image') {
                $src = $node['attrs']['src'] ?? null;
            }
        });

        expect($src)->toBe('https://existing.test/file.jpg');
    });

    it('does not attempt to resolve image `src` when `data-id` is blank', function (): void {
        $calls = 0;

        $editor = makeStateCastEditor();
        $editor->getFileAttachmentUrlUsing(function () use (&$calls): string {
            $calls++;

            return 'never-called';
        });

        $cast = new RichEditorStateCast($editor);

        $cast->set('<p><img src="https://literal.test/file.jpg" /></p>');

        expect($calls)->toBe(0);
    });

    it('hydrates a custom block `label` and `preview` from the registered block class', function (): void {
        $editor = makeStateCastEditor()
            ->customBlocks([StateCastCustomBlock::class]);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<div data-type="customBlock" data-id="preview-block"></div>');

        $found = null;

        walkStateCastResult($result, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found['attrs']['label'] ?? null)->toBe('Custom preview label');
        expect(base64_decode($found['attrs']['preview'] ?? ''))->toBe('<strong>Preview HTML</strong>');
    });

    it('does not hydrate `label` or `preview` for custom block nodes with unregistered ids', function (): void {
        $editor = makeStateCastEditor()
            ->customBlocks([StateCastCustomBlock::class]);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<div data-type="customBlock" data-id="unknown-block"></div>');

        $found = null;

        walkStateCastResult($result, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found['attrs']['label'] ?? null)->toBeNull();
        expect($found['attrs']['preview'] ?? null)->not->toContain('Preview HTML');
    });

    it('hydrates mention labels through a matching `MentionProvider`', function (): void {
        $editor = makeStateCastEditor()
            ->mentions([
                MentionProvider::make('@')->items(['1' => 'Alice', '2' => 'Bob']),
            ]);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<p><span data-type="mention" data-id="1" data-char="@" data-label="stale"></span></p>');

        $label = null;

        walkStateCastResult($result, function (array $node) use (&$label): void {
            if (($node['type'] ?? null) === 'mention') {
                $label = $node['attrs']['label'] ?? null;
            }
        });

        expect($label)->toBe('Alice');
    });

    it('sets the mention label to an empty string when the id is not in the provider results', function (): void {
        $editor = makeStateCastEditor()
            ->mentions([
                MentionProvider::make('@')->items(['1' => 'Alice']),
            ]);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<p><span data-type="mention" data-id="999" data-char="@" data-label="stale"></span></p>');

        $label = 'not-visited';

        walkStateCastResult($result, function (array $node) use (&$label): void {
            if (($node['type'] ?? null) === 'mention') {
                $label = $node['attrs']['label'] ?? null;
            }
        });

        expect($label)->toBe('');
    });

    it('wraps a list item whose first child is a text node into a paragraph', function (): void {
        $cast = new RichEditorStateCast(makeStateCastEditor());

        $result = $cast->set('<ul><li>Direct text</li></ul>');

        $listItemChildTypes = [];

        walkStateCastResult($result, function (array $node) use (&$listItemChildTypes): void {
            if (($node['type'] ?? null) === 'listItem') {
                $listItemChildTypes = array_column($node['content'] ?? [], 'type');
            }
        });

        expect($listItemChildTypes)->toBe(['paragraph']);
    });

    it('leaves a list item alone when its first child is already a paragraph', function (): void {
        $cast = new RichEditorStateCast(makeStateCastEditor());

        $result = $cast->set('<ul><li><p>Already wrapped</p></li></ul>');

        $listItemChildTypes = [];

        walkStateCastResult($result, function (array $node) use (&$listItemChildTypes): void {
            if (($node['type'] ?? null) === 'listItem') {
                $listItemChildTypes = array_column($node['content'] ?? [], 'type');
            }
        });

        expect($listItemChildTypes)->toBe(['paragraph']);
    });
});

describe('`get()`', function (): void {
    it('returns an HTML string when `isJson()` is `false`', function (): void {
        $cast = new RichEditorStateCast(makeStateCastEditor());

        $document = $cast->set('<p>Hello</p>');

        expect($cast->get($document))->toBeString()->toContain('Hello');
    });

    it('returns an array document when `isJson()` is `true`', function (): void {
        $editor = makeStateCastEditor()->json();

        $cast = new RichEditorStateCast($editor);

        $document = $cast->set('<p>Hello</p>');

        $output = $cast->get($document);

        expect($output)->toBeArray();
        expect($output['type'] ?? null)->toBe('doc');
    });

    it('strips image `src` when the file attachments visibility is `private`', function (): void {
        $editor = makeStateCastEditor()
            ->fileAttachmentsVisibility('private')
            ->getFileAttachmentUrlUsing(static fn (string $file): string => "https://signed.test/{$file}");

        $cast = new RichEditorStateCast($editor);

        $document = $cast->set('<p><img src="x" data-id="uploads/a.jpg" /></p>');

        $html = $cast->get($document);

        expect($html)->toBeString();
        expect($html)->not->toContain('https://signed.test');
    });

    it('preserves image `src` when the file attachments visibility is `public`', function (): void {
        $editor = makeStateCastEditor()
            ->fileAttachmentsVisibility('public')
            ->getFileAttachmentUrlUsing(static fn (string $file): string => "https://public.test/{$file}");

        $cast = new RichEditorStateCast($editor);

        $document = $cast->set('<p><img src="x" data-id="uploads/a.jpg" /></p>');

        $html = $cast->get($document);

        expect($html)->toContain('https://public.test/uploads/a.jpg');
    });

    it('strips `label` and `preview` attrs from custom blocks before returning output', function (): void {
        $editor = makeStateCastEditor()
            ->customBlocks([StateCastCustomBlock::class])
            ->json();

        $cast = new RichEditorStateCast($editor);

        $document = $cast->set('<div data-type="customBlock" data-id="preview-block"></div>');

        $output = $cast->get($document);

        $found = null;

        walkStateCastResult($output, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found['attrs']['label'] ?? null)->toBeNull();
        expect($found['attrs']['preview'] ?? null)->toBeNull();
        expect($found['attrs']['id'] ?? null)->toBe('preview-block');
    });

    it('handles `null` state without error', function (): void {
        $cast = new RichEditorStateCast(makeStateCastEditor());

        expect($cast->get(null))->toBeString();
    });
});

class StateCastCustomBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'preview-block';
    }

    public static function getPreviewLabel(array $config): string
    {
        return 'Custom preview label';
    }

    public static function toPreviewHtml(array $config): ?string
    {
        return '<strong>Preview HTML</strong>';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div>Rendered block</div>';
    }
}
