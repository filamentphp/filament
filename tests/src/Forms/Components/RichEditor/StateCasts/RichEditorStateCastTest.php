<?php

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\RichEditor\StateCasts\RichEditorStateCast;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\PostWithRichContent;
use Filament\Tests\Fixtures\RichEditor\TestRichContentPlugin;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Tiptap\Editor;
use Tiptap\Marks\Bold;
use Tiptap\Nodes\Paragraph;

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

    it('resolves image `src` from `data-id` using the editor\'s `getFileAttachmentUrlUsing()` callback', function (): void {
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

    it('falls back to `getFileAttachmentUrlFromAnotherRecord()` when the primary resolver returns `null`', function (): void {
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

    it('keeps the existing `src` without a provider when both resolvers return `null`', function (): void {
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

    it('only hydrates provider-backed images from trusted resolver URLs', function (?string $customUrl, ?string $crossRecordUrl, ?string $expectedUrl): void {
        $editor = makeStateCastEditor();
        $editor->getContainer()->model(new PostWithRichContent);
        $editor->getFileAttachmentUrlFromAnotherRecordUsing(static fn (): ?string => $crossRecordUrl);

        if ($customUrl !== null) {
            $editor->getFileAttachmentUrlUsing(static fn (): string => $customUrl);
        }

        expect($editor->getFileAttachmentProvider()->getFileAttachmentUrl('rejected-id'))->toBeNull();

        $cast = new RichEditorStateCast($editor);
        $result = $cast->set('<p><img src="https://existing.test/file.jpg" data-id="rejected-id" /></p>');
        $imageAttributes = null;

        walkStateCastResult($result, function (array $node) use (&$imageAttributes): void {
            if (($node['type'] ?? null) === 'image') {
                $imageAttributes = $node['attrs'];
            }
        });

        expect($imageAttributes)->toMatchArray(['id' => 'rejected-id', 'src' => $expectedUrl]);
    })->with([
        'provider rejection' => [null, null, null],
        'custom URL' => ['https://custom.test/file.jpg', null, 'https://custom.test/file.jpg'],
        'authorized cross-record URL' => [null, 'https://other.test/file.jpg', 'https://other.test/file.jpg'],
        'custom URL takes precedence' => ['https://custom.test/file.jpg', 'https://other.test/file.jpg', 'https://custom.test/file.jpg'],
    ]);

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

        $result = $cast->set([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'preview-block',
                        'config' => ['source' => 'persisted'],
                        'label' => 'Untrusted label',
                        'preview' => base64_encode('<img src="x" onerror="alert(document.domain)">'),
                        'shouldApplyProseStylingToPreview' => true,
                    ],
                ],
            ],
        ]);

        $found = null;

        walkStateCastResult($result, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found['attrs']['label'] ?? null)->toBe('Custom preview label');
        expect(base64_decode($found['attrs']['preview'] ?? ''))->toBe('<strong>Preview HTML</strong>');
        expect($found['attrs']['shouldApplyProseStylingToPreview'] ?? null)->toBeFalse();
        expect($found['attrs']['config'] ?? null)->toBe(['source' => 'persisted']);
    });

    it('passes array configuration to custom block preview callbacks from every stored format', function (string $format, mixed $config, array $expectedConfig): void {
        $cast = new RichEditorStateCast(makeStateCastEditor()->customBlocks([StateCastConfigBlock::class]));
        $document = [
            'type' => 'doc',
            'content' => [['type' => 'customBlock', 'attrs' => ['id' => 'config-block', 'config' => $config]]],
        ];
        $content = match ($format) {
            'HTML' => '<div data-type="customBlock" data-id="config-block" data-config="' . e(json_encode($config)) . '"></div>',
            'JSON' => json_encode($document),
            default => $document,
        };

        $result = $cast->set($content);
        $attributes = $result['content'][0]['attrs'];

        expect(json_decode($attributes['label'], associative: true, flags: JSON_THROW_ON_ERROR))->toBe($expectedConfig)
            ->and(base64_decode($attributes['preview']))->toBe('<p>' . e(json_encode($expectedConfig)) . '</p>')
            ->and($attributes['shouldApplyProseStylingToPreview'])->toBe($expectedConfig['prose'] ?? false);
    })->with(['HTML', 'JSON', 'array'])->with([
        'string' => ['invalid', []],
        'true' => [true, []],
        'false' => [false, []],
        'integer' => [42, []],
        'float' => [1.5, []],
        'null' => [null, []],
        'object' => [(object) ['nested' => (object) ['enabled' => false]], ['nested' => ['enabled' => false]]],
        'list' => [['first', ['second' => false]], ['first', ['second' => false]]],
        'nested configuration' => [
            ['heading' => 'Café <hero>', 'prose' => true, 'extension' => ['type' => 'custom', 'content' => [0, false, null]]],
            ['heading' => 'Café <hero>', 'prose' => true, 'extension' => ['type' => 'custom', 'content' => [0, false, null]]],
        ],
    ]);

    it('uses empty configuration for malformed HTML custom block JSON', function (string $config): void {
        $cast = new RichEditorStateCast(makeStateCastEditor()->customBlocks([StateCastConfigBlock::class]));

        $result = $cast->set('<div data-type="customBlock" data-id="config-block" data-config="' . e($config) . '"></div>');

        expect($result['content'][0]['attrs']['label'])->toBe('[]');
    })->with(['{broken', '{"text":"\uD800"}', '1e400', '{"limit":1e400}']);

    it('ignores stored HTML previews before serializing or regenerating custom blocks', function (string $direction, string $preview): void {
        $cast = new RichEditorStateCast(makeStateCastEditor()->json()->customBlocks([StateCastCustomBlock::class]));

        $result = $cast->{$direction}('<div data-type="customBlock" data-id="preview-block" data-preview="' . e($preview) . '"></div><p>Preserved sibling</p>');

        expect(json_encode($result, JSON_THROW_ON_ERROR))->toContain('Preserved sibling');

        if ($direction === 'set') {
            expect(base64_decode($result['content'][0]['attrs']['preview']))->toBe('<strong>Preview HTML</strong>');
        } else {
            expect($result['content'][0]['attrs'])->not->toHaveKey('preview');
        }
    })->with(['get', 'set'])->with([
        'invalid UTF-8' => base64_encode("\xB1\x31"),
        'malformed base64' => '%%%/w==',
        'obsolete HTML' => base64_encode('<p>Obsolete preview</p>'),
    ]);

    it('hydrates a custom block `shouldApplyProseStylingToPreview` from the registered block class', function (): void {
        $editor = makeStateCastEditor()
            ->customBlocks([StateCastCustomBlock::class, StateCastProseBlock::class]);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<div data-type="customBlock" data-id="preview-block"></div>');

        $found = null;

        walkStateCastResult($result, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found['attrs']['shouldApplyProseStylingToPreview'] ?? null)->toBeFalse();
    });

    it('hydrates `shouldApplyProseStylingToPreview` as `true` when the block class enables it', function (): void {
        $editor = makeStateCastEditor()
            ->customBlocks([StateCastProseBlock::class]);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<div data-type="customBlock" data-id="prose-block"></div>');

        $found = null;

        walkStateCastResult($result, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found['attrs']['shouldApplyProseStylingToPreview'] ?? null)->toBeTrue();
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
        expect($found['attrs']['preview'] ?? null)->toBeNull();
    });

    it('strips persisted custom block preview attributes from unregistered blocks', function (array $registeredCustomBlocks, ?string $id): void {
        $editor = makeStateCastEditor()
            ->customBlocks($registeredCustomBlocks);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => $id,
                        'config' => ['source' => 'persisted'],
                        'label' => 'Untrusted label',
                        'preview' => base64_encode('<img src="x" onerror="alert(document.domain)">'),
                        'shouldApplyProseStylingToPreview' => true,
                    ],
                ],
            ],
        ]);

        $found = null;

        walkStateCastResult($result, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found)->not->toBeNull();
        expect($found['attrs']['id'] ?? null)->toBe($id);
        expect($found['attrs']['config'] ?? null)->toBe(['source' => 'persisted']);
        expect($found['attrs'])
            ->not->toHaveKey('label')
            ->not->toHaveKey('preview')
            ->not->toHaveKey('shouldApplyProseStylingToPreview');
    })->with([
        'without registered custom blocks' => [[], 'unknown-block'],
        'with registered custom blocks' => [[StateCastCustomBlock::class], 'unknown-block'],
        'without an id' => [[StateCastCustomBlock::class], null],
    ]);

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

    it('replaces mention extra attributes with those configured by the matching provider', function (): void {
        $editor = makeStateCastEditor()
            ->mentions([
                MentionProvider::make('@')
                    ->items(['1' => 'Alice'])
                    ->extraAttributes(['data-provider' => 'trusted']),
            ]);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set([
            'type' => 'doc',
            'content' => [[
                'type' => 'paragraph',
                'content' => [[
                    'type' => 'mention',
                    'attrs' => [
                        'id' => '1',
                        'char' => '@',
                        'href' => 'javascript:alert(document.domain)',
                        'extra' => [
                            'x-init' => 'alert(document.domain)',
                            'data-attacker' => 'stored',
                        ],
                    ],
                ]],
            ]],
        ]);

        $mentionAttributes = null;

        walkStateCastResult($result, function (array $node) use (&$mentionAttributes): void {
            if (($node['type'] ?? null) === 'mention') {
                $mentionAttributes = $node['attrs'] ?? null;
            }
        });

        expect($mentionAttributes['extra'] ?? null)->toBe(['data-provider' => 'trusted'])
            ->and($mentionAttributes)->not->toHaveKey('href');
    });

    it('preserves a mention with a `data-id` of `0` when hydrating HTML', function (): void {
        $editor = makeStateCastEditor()
            ->mentions([
                MentionProvider::make('@')->items([0 => 'Alice']),
            ]);

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<p><span data-type="mention" data-id="0" data-char="@"></span></p>');

        $mention = null;

        walkStateCastResult($result, function (array $node) use (&$mention): void {
            if (($node['type'] ?? null) === 'mention') {
                $mention = $node;
            }
        });

        expect($mention['attrs']['id'] ?? null)->toBe('0')
            ->and($mention['attrs']['label'] ?? null)->toBe('Alice');
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

    it('replaces client-supplied mention extra attributes before returning JSON state', function (): void {
        $editor = makeStateCastEditor()
            ->json()
            ->mentions([
                MentionProvider::make('@')
                    ->items(['1' => 'Alice'])
                    ->extraAttributes(['data-provider' => 'trusted']),
            ]);

        $cast = new RichEditorStateCast($editor);

        $output = $cast->get([
            'type' => 'doc',
            'content' => [[
                'type' => 'paragraph',
                'content' => [[
                    'type' => 'mention',
                    'attrs' => [
                        'id' => '1',
                        'char' => '@',
                        'href' => 'javascript:alert(document.domain)',
                        'extra' => [
                            'x-init' => 'alert(document.domain)',
                            'data-attacker' => 'stored',
                        ],
                    ],
                ]],
            ]],
        ]);

        $mentionAttributes = null;

        walkStateCastResult($output, function (array $node) use (&$mentionAttributes): void {
            if (($node['type'] ?? null) === 'mention') {
                $mentionAttributes = $node['attrs'] ?? null;
            }
        });

        expect($mentionAttributes['extra'] ?? null)->toBe(['data-provider' => 'trusted'])
            ->and($mentionAttributes)->not->toHaveKey('href');
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

    it('strips custom block preview attributes before returning output when no custom blocks are registered', function (): void {
        $editor = makeStateCastEditor()->json();

        $cast = new RichEditorStateCast($editor);

        $output = $cast->get([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'unknown-block',
                        'config' => ['source' => 'persisted'],
                        'label' => 'Untrusted label',
                        'preview' => base64_encode('<img src="x" onerror="alert(document.domain)">'),
                        'shouldApplyProseStylingToPreview' => true,
                    ],
                ],
            ],
        ]);

        $found = null;

        walkStateCastResult($output, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found)->not->toBeNull();
        expect($found['attrs']['id'] ?? null)->toBe('unknown-block');
        expect($found['attrs']['config'] ?? null)->toBe(['source' => 'persisted']);
        expect($found['attrs'])
            ->not->toHaveKey('label')
            ->not->toHaveKey('preview')
            ->not->toHaveKey('shouldApplyProseStylingToPreview');
    });

    it('strips `shouldApplyProseStylingToPreview` attr from custom blocks before returning output', function (): void {
        $editor = makeStateCastEditor()
            ->customBlocks([StateCastProseBlock::class])
            ->json();

        $cast = new RichEditorStateCast($editor);

        $document = $cast->set('<div data-type="customBlock" data-id="prose-block"></div>');

        $output = $cast->get($document);

        $found = null;

        walkStateCastResult($output, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found['attrs']['shouldApplyProseStylingToPreview'] ?? null)->toBeNull();
        expect($found['attrs']['id'] ?? null)->toBe('prose-block');
    });

    it('removes `label` and `preview` attrs even when they do not exist yet in the custom block', function (): void {
        $editor = makeStateCastEditor()
            ->customBlocks([StateCastCustomBlock::class])
            ->json();

        $cast = new RichEditorStateCast($editor);

        $document = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'preview-block',
                        'config' => null,
                    ],
                ],
            ],
        ];

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

    it('removes `shouldApplyProseStylingToPreview` attr even when it does not exist yet in the custom block', function (): void {
        $editor = makeStateCastEditor()
            ->customBlocks([StateCastProseBlock::class])
            ->json();

        $cast = new RichEditorStateCast($editor);

        $document = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'prose-block',
                        'config' => null,
                    ],
                ],
            ],
        ];

        $output = $cast->get($document);

        $found = null;

        walkStateCastResult($output, function (array $node) use (&$found): void {
            if (($node['type'] ?? null) === 'customBlock') {
                $found = $node;
            }
        });

        expect($found['attrs']['shouldApplyProseStylingToPreview'] ?? null)->toBeNull();
        expect($found['attrs']['id'] ?? null)->toBe('prose-block');
    });

    it('handles `null` state without error', function (): void {
        $cast = new RichEditorStateCast(makeStateCastEditor());

        expect($cast->get(null))->toBeString();
    });
});

it('discards unencodable custom block configuration without losing siblings during state casting', function (string $direction, string $config, bool $isJson): void {
    $cast = new RichEditorStateCast(makeStateCastEditor()->json()->customBlocks([StateCastConfigBlock::class]));
    $document = '{"type":"doc","content":[{"type":"customBlock","attrs":{"id":"config-block","config":' . $config . '}},{"type":"paragraph","content":[{"type":"text","text":"Preserved sibling"}]}]}';

    $result = $cast->{$direction}($isJson ? $document : json_decode($document, associative: true));

    expect($result['content'][0]['attrs']['config'])->toBeNull()
        ->and($result['content'][1]['content'][0]['text'])->toBe('Preserved sibling')
        ->and(json_encode($result, JSON_THROW_ON_ERROR))->toBeString();

    if ($direction === 'set') {
        expect($result['content'][0]['attrs']['label'])->toBe('[]');
    }
})->with(['get', 'set'])->with(['1e400', '{"limit":1e400}'])->with([true, false]);

it('preserves mentions with missing attributes during state casting', function (string $direction, array $attributes): void {
    $cast = new RichEditorStateCast(makeStateCastEditor()->json());

    $result = $cast->{$direction}([
        'type' => 'doc',
        'content' => [[
            'type' => 'paragraph',
            'content' => [['type' => 'mention', ...$attributes]],
        ]],
    ]);

    $mentions = [];

    walkStateCastResult($result, function (array $node) use (&$mentions): void {
        if (($node['type'] ?? null) === 'mention') {
            $mentions[] = $node;
        }
    });

    expect($mentions)->toHaveCount(1)
        ->and($mentions[0]['attrs']['extra'])->toBeNull();
})->with(['get', 'set'])->with([
    'omitted' => [[]],
    'null' => [['attrs' => null]],
]);

describe('document boundary', function (): void {
    it('does not persist JSON that fails later rich content rendering', function (?string $attribute): void {
        $document = [
            'type' => 'doc',
            'content' => [[
                'type' => 'grid',
                'attrs' => ['data-cols' => ($attribute === 'data-cols') ? ['invalid' => 3] : 3],
                'content' => [[
                    'type' => 'gridColumn',
                    'attrs' => ['data-col-span' => ($attribute === 'data-col-span') ? ['invalid' => 2] : 2],
                    'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Grid content']]]],
                ]],
            ]],
        ];
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([$editor = RichEditor::make('content')->json()])
            ->fill();
        $editor->rawState($document);

        $savedDocument = $schema->getState()['content'];
        $html = RichContentRenderer::make($savedDocument)->toHtml();
        $expectedDocument = ($attribute === null) ? $document : ['type' => 'doc', 'content' => []];

        expect($savedDocument)->toBe($expectedDocument);

        if ($attribute === null) {
            expect($html)->toContain('Grid content', '--cols: repeat(3, minmax(0, 1fr))', '--col-span: span 2 / span 2');
        } else {
            expect($html)->toBe('');
        }

        $schema->fill(['content' => $document]);

        expect($editor->getRawState())->toBe($expectedDocument);
    })->with(['data-col-span' => ['data-col-span'], 'data-cols' => ['data-cols'], 'valid' => [null]]);

    it('defaults malformed mention trigger characters before looking up providers', function (string $direction, bool $hasProvider): void {
        $providers = $hasProvider ? [MentionProvider::make('@')->items(['1' => 'Alice'])] : [];
        $cast = new RichEditorStateCast(makeStateCastEditor()->json()->mentions($providers));
        $result = $cast->{$direction}([
            'type' => 'doc',
            'content' => [['type' => 'paragraph', 'content' => [
                ['type' => 'mention', 'attrs' => ['id' => '1', 'char' => ['invalid' => '@'], 'label' => 'Alice']],
                ['type' => 'text', 'text' => 'Kept sibling'],
            ]]],
        ]);

        expect($result['content'][0]['content'][0]['attrs']['char'] ?? '@')->toBe('@')
            ->and(html_entity_decode(strip_tags(RichContentRenderer::make($result)->mentions($providers)->toHtml())))->toBe('@AliceKept sibling');
    })->with(['get', 'set'])->with([false, true]);

    it('already fails closed for HTML or text that is valid JSON', function (string $direction, string $content): void {
        $cast = new RichEditorStateCast(makeStateCastEditor()->json());

        expect($cast->{$direction}($content))->toBe(['type' => 'doc', 'content' => []]);
    })->with(['get', 'set'])->with(['"<p>Quoted HTML</p>"', '"Quoted text"', '123', 'true', 'null', '[]']);

    it('preserves bracketed plain text that is not valid JSON', function (string $direction, int $depth): void {
        $cast = new RichEditorStateCast(makeStateCastEditor()->json());
        $text = str_repeat('[', $depth) . 'Not JSON';

        expect(RichContentRenderer::make($cast->{$direction}($text))->toText())->toBe($text);
    })->with(['get', 'set'])->with([101, 513]);

    it('drops malformed text nodes without losing valid text or marks', function (string $direction, bool $isEncoded): void {
        $cast = new RichEditorStateCast(makeStateCastEditor()->json());
        $document = [
            'type' => 'doc',
            'content' => [[
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text'],
                    ...array_map(static fn (mixed $text): array => ['type' => 'text', 'text' => $text], [null, false, 0, 1.5, [], ['bad'], (object) ['bad' => true], '']),
                    ['type' => 'text', 'text' => '0', 'marks' => [['type' => 'bold']]],
                ],
            ]],
        ];

        $result = $cast->{$direction}($isEncoded ? json_encode($document) : $document);

        expect($result['content'][0]['content'])->toBe([
            ['type' => 'text', 'text' => '0', 'marks' => [['type' => 'bold']]],
        ]);
        expect(RichContentRenderer::make($result)->toHtml())->toBe('<p><strong>0</strong></p>');
    })->with(['get', 'set'])->with([false, true]);

    it('normalizes malformed node and mark attribute containers before rendering', function (string $direction, mixed $attributes): void {
        $cast = new RichEditorStateCast(makeStateCastEditor()->json());
        $result = $cast->{$direction}([
            'type' => 'doc',
            'content' => [[
                'type' => 'paragraph',
                'attrs' => $attributes,
                'content' => [['type' => 'text', 'text' => 'Kept', 'marks' => [['type' => 'link', 'attrs' => $attributes]]]],
            ], [
                'type' => 'mention',
                'attrs' => $attributes,
            ]],
        ]);

        expect($result['content'][0])->not->toHaveKey('attrs')
            ->and($result['content'][0]['content'][0]['marks'][0])->not->toHaveKey('attrs')
            ->and($result['content'][1]['attrs']['extra'])->toBeNull();
        expect(RichContentRenderer::make($result)->toHtml())->toContain('Kept');
    })->with(['get', 'set'])->with([
        'false' => [false],
        'true' => [true],
        'number' => [12],
        'string' => ['invalid'],
        'empty array' => [[]],
        'list' => [['invalid']],
        'empty object' => [(object) []],
    ]);

    it('removes non-string fallback text before rendering non-text nodes', function (string $direction): void {
        $cast = new RichEditorStateCast(makeStateCastEditor()->json());
        $result = $cast->{$direction}(['type' => 'doc', 'content' => [['type' => 'paragraph', 'text' => ['invalid']]]]);

        expect($result['content'])->toBe([['type' => 'paragraph']]);
        expect(RichContentRenderer::make($result)->toHtml())->toBe('<p></p>');
    })->with(['get', 'set']);

    it('bounds document serialization depth before TipTap and Livewire traverse it', function (string $direction, bool $isEncoded, int $depth): void {
        $cast = new RichEditorStateCast(makeStateCastEditor()->json());
        $document = ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Deep text', 'attrs' => ['locale' => 'en']]]];

        for ($level = 0; $level < $depth; $level++) {
            $document = ['type' => 'blockquote', 'content' => [$document]];
        }

        $document = ['type' => 'doc', 'content' => [$document]];
        $result = $cast->{$direction}($isEncoded ? json_encode($document, depth: 1024) : $document);

        if ($depth > 47) {
            expect($result['type'])->toBe('doc')
                ->and($result['content'])->toBeEmpty();

            return;
        }

        expect($result)->toBe($document);
        expect(RichContentRenderer::make($result)->toText())->toBe('Deep text');
    })->with(['get', 'set'])->with([false, true])->with([47, 48, 240]);

    it('preserves bound editors and valid string-typed plugin nodes and marks through hydration and storage', function (): void {
        $editor = makeStateCastEditor()->json()->plugins([
            new class extends TestRichContentPlugin
            {
                public function getTipTapPhpExtensions(): array
                {
                    return [app(StateCastPluginNode::class), app(StateCastPluginMark::class)];
                }
            },
        ]);
        $cast = app(RichEditorStateCast::class, ['richEditor' => $editor]);
        $document = [
            'type' => 'doc',
            'content' => [[
                'type' => 'pluginParagraph',
                'attrs' => ['settings' => ['enabled' => true, 'weights' => [3, 7]]],
                'content' => [['type' => 'text', 'text' => 'Plugin content', 'marks' => [[
                    'type' => 'pluginBold',
                    'attrs' => ['settings' => ['tokens' => ['first', 'second']]],
                ]]]],
            ]],
        ];
        $boundEditor = $editor->getTipTapEditor();
        app()->bind(Editor::class, static fn (): Editor => $boundEditor);

        expect($cast->get($cast->set($document)))->toBe($document)
            ->and($editor->getTipTapEditor())->toBe($boundEditor);

        $editor->json(false);

        expect($cast->get($document))->toBe('<p><strong>Plugin content</strong></p>');
    });

    it('drops nested non-string node types without losing valid siblings or trusting previews', function (string $direction, bool $isEncoded): void {
        $cast = app(RichEditorStateCast::class, ['richEditor' => makeStateCastEditor()->json()]);
        $untrustedNodes = array_map(static fn (mixed $type): array => [
            'type' => $type,
            'attrs' => [
                'id' => 'unknown-block',
                'label' => 'Untrusted label',
                'preview' => base64_encode('<span data-untrusted-preview="true">Marker</span>'),
                'shouldApplyProseStylingToPreview' => true,
            ],
            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Discarded subtree']]]],
        ], [true, false, 1, 1.5, null, ['customBlock'], (object) ['name' => 'customBlock']]);
        $document = [
            'type' => 'doc',
            'content' => [[
                'type' => 'blockquote',
                'content' => [
                    ...$untrustedNodes,
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Preserved sibling']]],
                ],
            ]],
        ];

        $result = $cast->{$direction}($isEncoded ? json_encode($document) : $document);

        expect($result['content'][0]['content'])->toBe([
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Preserved sibling']]],
        ])
            ->and(json_encode($result))->not->toContain('preview', 'Untrusted label', 'Discarded subtree');
    })->with(['get', 'set'])->with([false, true]);

    it('drops non-string mark types while preserving text and valid marks', function (string $direction): void {
        $cast = app(RichEditorStateCast::class, ['richEditor' => makeStateCastEditor()->json()]);
        $untrustedMarks = array_map(static fn (mixed $type): array => ['type' => $type], [true, false, 1, 1.5, null, ['bold'], (object) ['name' => 'bold']]);

        $result = $cast->{$direction}([
            'type' => 'doc',
            'content' => [[
                'type' => 'paragraph',
                'content' => [[
                    'type' => 'text',
                    'text' => 'Preserved text',
                    'marks' => [...$untrustedMarks, ['type' => 'bold']],
                ]],
            ]],
        ]);

        expect($result['content'][0]['content'][0])->toBe([
            'type' => 'text',
            'text' => 'Preserved text',
            'marks' => [['type' => 'bold']],
        ]);
    })->with(['get', 'set']);

    it('fails closed for malformed document roots', function (string $direction, mixed $document): void {
        $cast = app(RichEditorStateCast::class, ['richEditor' => makeStateCastEditor()->json()]);

        expect($cast->{$direction}($document))
            ->toBe(['type' => 'doc', 'content' => []]);
    })->with(['get', 'set'])->with([
        'boolean type' => [['type' => true, 'content' => [['type' => 'paragraph']]]],
        'integer type' => [['type' => 1, 'content' => []]],
        'array type' => [['type' => ['doc'], 'content' => []]],
        'missing type' => [['content' => [['type' => 'paragraph']]]],
        'non-document type' => [['type' => 'text', 'text' => 'Root text', 'content' => [['type' => 'image', 'attrs' => ['id' => 'foreign.jpg']]]]],
        'list' => [[['type' => 'paragraph']]],
        'scalar' => [true],
        'encoded root' => ['{"type":true,"content":[{"type":"paragraph"}]}'],
    ]);
});

class StateCastPluginNode extends Paragraph
{
    public static $name = 'pluginParagraph';
}

class StateCastPluginMark extends Bold
{
    public static $name = 'pluginBold';
}

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

class StateCastConfigBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'config-block';
    }

    public static function getPreviewLabel(array $config): string
    {
        return json_encode($config, JSON_THROW_ON_ERROR);
    }

    public static function toPreviewHtml(array $config): string
    {
        return '<p>' . e(json_encode($config, JSON_THROW_ON_ERROR)) . '</p>';
    }

    public static function shouldApplyProseStylingToPreview(array $config): bool
    {
        return $config['prose'] ?? false;
    }
}

class StateCastProseBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'prose-block';
    }

    public static function getPreviewLabel(array $config): string
    {
        return 'Prose block';
    }

    public static function toPreviewHtml(array $config): ?string
    {
        return '<h2>With Prose Styling</h2>';
    }

    public static function shouldApplyProseStylingToPreview(array $config): bool
    {
        return true;
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div>Rendered prose block</div>';
    }
}
