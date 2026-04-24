<?php

use Filament\Forms\Components\RichEditor\FileAttachmentProviders\Contracts\FileAttachmentProvider;
use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\HasFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichContentAttribute;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\RichEditor\TextColor;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

uses(TestCase::class);

describe('merge tags', function (): void {
    it('processes merge tags with string values as text nodes', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'name',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'name' => 'John Doe',
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('John Doe');
    });

    it('processes merge tags with `Htmlable` values as raw HTML nodes', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'formatted_name',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'formatted_name' => new HtmlString('<strong>John Doe</strong>'),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<strong>John Doe</strong>');
    });

    it('handles mixed `Htmlable` and non-`Htmlable` values in the same merge tags array', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'plain_text',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => ' and ',
                        ],
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'html_content',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'plain_text' => 'Regular text',
            'html_content' => new HtmlString('<em>emphasized text</em>'),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('Regular text');
        expect($html)->toContain('<em>emphasized text</em>');
    });

    it('calls `toHtml()` on `Htmlable` instances for merge tag values', function (): void {
        $htmlable = new class implements Htmlable
        {
            public function toHtml(): string
            {
                return '<div class="custom">Custom HTML content</div>';
            }
        };

        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'custom',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'custom' => $htmlable,
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<div class="custom">Custom HTML content</div>');
    });

    it('handles complex HTML structures from `Htmlable` instances for merge tag values', function (): void {
        $complexHtml = new HtmlString('
        <div class="card">
            <h3>Title</h3>
            <p>Description with <a href="https://example.com">link</a></p>
            <ul>
                <li>Item 1</li>
                <li>Item 2</li>
            </ul>
        </div>
    ');

        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'complex',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'complex' => $complexHtml,
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<div class="card">');
        expect($html)->toContain('<h3>Title</h3>');
        expect($html)->toContain('<a href="https://example.com">link</a>');
        expect($html)->toContain('<ul>');
    });

    it('handles `null` and empty merge tag values correctly', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'null_value',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => ' | ',
                        ],
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'empty_string',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => ' | ',
                        ],
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'empty_html',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'null_value' => null,
            'empty_string' => '',
            'empty_html' => new HtmlString(''),
        ]);

        $html = $renderer->toUnsafeHtml();

        // Should not throw errors and should render the separators
        expect($html)->toContain(' | ');
    });

    it('renders complete document with mixed merge tag content types', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'heading',
                    'attrs' => ['level' => 1],
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Welcome ',
                        ],
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'user_name',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Your profile: ',
                        ],
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'profile_card',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Status: ',
                        ],
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'status',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'user_name' => 'John Doe',
            'profile_card' => new HtmlString('<div class="profile"><img src="avatar.jpg" alt="Avatar"><span>Premium User</span></div>'),
            'status' => 'Active',
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('Welcome <span data-type="mergeTag" data-id="user_name">John Doe</span>');
        expect($html)->toContain('<div class="profile"><img src="avatar.jpg" alt="Avatar"><span>Premium User</span></div>');
        expect($html)->toContain('Status: <span data-type="mergeTag" data-id="status">Active</span>');
    });

    it('handles nested HTML structures in merge tags', function (): void {
        $nestedHtml = new HtmlString('
        <div class="notification">
            <div class="header">
                <strong>Alert</strong>
                <span class="badge">New</span>
            </div>
            <div class="body">
                <p>You have <a href="/messages">3 new messages</a></p>
                <ul class="actions">
                    <li><button type="button">Mark as Read</button></li>
                    <li><button type="button">Dismiss</button></li>
                </ul>
            </div>
        </div>
    ');

        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'notification',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'notification' => $nestedHtml,
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<div class="notification">');
        expect($html)->toContain('<strong>Alert</strong>');
        expect($html)->toContain('<a href="/messages">3 new messages</a>');
        expect($html)->toContain('<button type="button">Mark as Read</button>');
    });

    it('processes multiple HTML merge tags in same document', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'header',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'content',
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'footer',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'header' => new HtmlString('<header><h1>Page Title</h1></header>'),
            'content' => new HtmlString('<main><p>Main content goes here</p></main>'),
            'footer' => new HtmlString('<footer><p>&copy; 2024 Company</p></footer>'),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<header><h1>Page Title</h1></header>');
        expect($html)->toContain('<main><p>Main content goes here</p></main>');
        expect($html)->toContain('<footer><p>&copy; 2024 Company</p></footer>');
    });

    it('handles HTML merge tags with special characters and entities', function (): void {
        $specialHtml = new HtmlString('<p>Price: $100 &amp; up • Available in &lt;24hrs&gt;</p>');

        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'price_info',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'price_info' => $specialHtml,
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('Price: $100 &amp; up • Available in &lt;24hrs&gt;');
    });

    it('handles dynamic merge tag values', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mergeTag',
                            'attrs' => [
                                'id' => 'dynamic_value',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mergeTags([
            'dynamic_value' => fn () => 'computed value',
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('computed value');
    });
});

describe('merge tag output', function (): void {
    it('wraps a string merge tag in a `<span>` with `data-type` and `data-id` via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p><span data-type="mergeTag" data-id="name">John</span></p>');
    });

    it('preserves `data-id` on the merge tag `<span>` via `toHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toHtml())
            ->toBe('<p><span data-type="mergeTag" data-id="name">John</span></p>');
    });

    it('renders only the text value via `toText()` for a string merge tag', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toText())->toBe('John');
    });

    it('nests a text node inside the merge tag node via `toArray()` for a string merge tag', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toArray())->toBe([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Hello '],
                        [
                            'type' => 'mergeTag',
                            'attrs' => ['id' => 'name'],
                            'content' => [
                                ['type' => 'text', 'text' => 'John'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    });

    it('renders a string merge tag inline with surrounding text via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                    ['type' => 'text', 'text' => ', welcome!'],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p>Hello <span data-type="mergeTag" data-id="name">John</span>, welcome!</p>');
    });

    it('renders a string merge tag inline with surrounding text via `toHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                    ['type' => 'text', 'text' => ', welcome!'],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toHtml())
            ->toBe('<p>Hello <span data-type="mergeTag" data-id="name">John</span>, welcome!</p>');
    });

    it('renders a string merge tag inline with surrounding text via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                    ['type' => 'text', 'text' => ', welcome!'],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toText())->toBe('Hello John, welcome!');
    });

    it('renders an `Htmlable` merge tag as raw HTML without a wrapping `<span>` via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'badge']],
                ]],
            ],
        ])->mergeTags(['badge' => new HtmlString('<strong>Admin</strong>')]);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p><strong>Admin</strong></p>');
    });

    it('renders an `Htmlable` merge tag as raw HTML without a wrapping `<span>` via `toHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'badge']],
                ]],
            ],
        ])->mergeTags(['badge' => new HtmlString('<strong>Admin</strong>')]);

        expect($renderer->toHtml())
            ->toBe('<p><strong>Admin</strong></p>');
    });

    it('extracts text from an `Htmlable` merge tag by stripping HTML via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'badge']],
                ]],
            ],
        ])->mergeTags(['badge' => new HtmlString('<strong>Admin</strong>')]);

        expect($renderer->toText())->toBe('Admin');
    });

    it('converts an `Htmlable` merge tag to a `rawHtmlMergeTag` node via `toArray()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ])->mergeTags(['name' => new HtmlString('<b>John</b>')]);

        expect($renderer->toArray())->toBe([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Hello '],
                        [
                            'type' => 'rawHtmlMergeTag',
                            'attrs' => ['id' => 'name'],
                            'html' => '<b>John</b>',
                        ],
                    ],
                ],
            ],
        ]);
    });

    it('renders an `Htmlable` merge tag inline with surrounding text via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Role: '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'badge']],
                    ['type' => 'text', 'text' => ' assigned'],
                ]],
            ],
        ])->mergeTags(['badge' => new HtmlString('<strong>Admin</strong>')]);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p>Role: <strong>Admin</strong> assigned</p>');
    });

    it('renders an `Htmlable` merge tag inline with surrounding text via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Role: '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'badge']],
                    ['type' => 'text', 'text' => ' assigned'],
                ]],
            ],
        ])->mergeTags(['badge' => new HtmlString('<strong>Admin</strong>')]);

        expect($renderer->toText())->toBe('Role: Admin assigned');
    });

    it('renders mixed string and `Htmlable` merge tags inline via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'greeting']],
                    ['type' => 'text', 'text' => ' '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'badge']],
                ]],
            ],
        ])->mergeTags([
            'greeting' => 'Hello',
            'badge' => new HtmlString('<strong>Admin</strong>'),
        ]);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p><span data-type="mergeTag" data-id="greeting">Hello</span> <strong>Admin</strong></p>');
    });

    it('renders mixed string and `Htmlable` merge tags via `toHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'greeting']],
                    ['type' => 'text', 'text' => ' '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'badge']],
                ]],
            ],
        ])->mergeTags([
            'greeting' => 'Hello',
            'badge' => new HtmlString('<strong>Admin</strong>'),
        ]);

        expect($renderer->toHtml())
            ->toBe('<p><span data-type="mergeTag" data-id="greeting">Hello</span> <strong>Admin</strong></p>');
    });

    it('renders mixed string and `Htmlable` merge tags inline via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'greeting']],
                    ['type' => 'text', 'text' => ' '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'badge']],
                ]],
            ],
        ])->mergeTags([
            'greeting' => 'Hello',
            'badge' => new HtmlString('<strong>Admin</strong>'),
        ]);

        expect($renderer->toText())->toBe('Hello Admin');
    });

    it('renders `null`, empty string, and missing merge tags as empty `<span>` elements via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'A'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'null_val']],
                    ['type' => 'text', 'text' => 'B'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'empty_str']],
                    ['type' => 'text', 'text' => 'C'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'missing']],
                    ['type' => 'text', 'text' => 'D'],
                ]],
            ],
        ])->mergeTags(['null_val' => null, 'empty_str' => '']);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p>A<span data-type="mergeTag" data-id="null_val"></span>B<span data-type="mergeTag" data-id="empty_str"></span>C<span data-type="mergeTag" data-id="missing"></span>D</p>');
    });

    it('renders `null`, empty string, and missing merge tags as empty `<span>` elements via `toHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'A'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'null_val']],
                    ['type' => 'text', 'text' => 'B'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'empty_str']],
                    ['type' => 'text', 'text' => 'C'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'missing']],
                    ['type' => 'text', 'text' => 'D'],
                ]],
            ],
        ])->mergeTags(['null_val' => null, 'empty_str' => '']);

        expect($renderer->toHtml())
            ->toBe('<p>A<span data-type="mergeTag" data-id="null_val"></span>B<span data-type="mergeTag" data-id="empty_str"></span>C<span data-type="mergeTag" data-id="missing"></span>D</p>');
    });

    it('renders `null`, empty string, and missing merge tags as empty inline content via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'A'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'null_val']],
                    ['type' => 'text', 'text' => 'B'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'empty_str']],
                    ['type' => 'text', 'text' => 'C'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'missing']],
                    ['type' => 'text', 'text' => 'D'],
                ]],
            ],
        ])->mergeTags(['null_val' => null, 'empty_str' => '']);

        expect($renderer->toText())->toBe('ABCD');
    });

    it('renders an unresolved merge tag as an empty `<span>` when no `mergeTags()` is called via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ]);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p><span data-type="mergeTag" data-id="name"></span></p>');
    });

    it('renders an unresolved merge tag as an empty `<span>` when no `mergeTags()` is called via `toHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ]);

        expect($renderer->toHtml())
            ->toBe('<p><span data-type="mergeTag" data-id="name"></span></p>');
    });

    it('renders an unresolved merge tag as empty text via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ]);

        expect($renderer->toText())->toBe('');
    });

    it('renders merge tags across multiple paragraphs via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Your role is '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'role']],
                    ['type' => 'text', 'text' => '.'],
                ]],
            ],
        ])->mergeTags(['name' => 'John', 'role' => new HtmlString('<em>admin</em>')]);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p>Hello <span data-type="mergeTag" data-id="name">John</span></p><p>Your role is <em>admin</em>.</p>');
    });

    it('renders merge tags across multiple paragraphs via `toHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Your role is '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'role']],
                    ['type' => 'text', 'text' => '.'],
                ]],
            ],
        ])->mergeTags(['name' => 'John', 'role' => new HtmlString('<em>admin</em>')]);

        expect($renderer->toHtml())
            ->toBe('<p>Hello <span data-type="mergeTag" data-id="name">John</span></p><p>Your role is <em>admin</em>.</p>');
    });

    it('renders merge tags inline within each paragraph, with paragraph separators preserved via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Your role is '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'role']],
                    ['type' => 'text', 'text' => '.'],
                ]],
            ],
        ])->mergeTags(['name' => 'John', 'role' => new HtmlString('<em>admin</em>')]);

        expect($renderer->toText())->toBe("Hello John\n\nYour role is admin.");
    });

    it('renders a string merge tag inside a heading via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [
                    ['type' => 'text', 'text' => 'Welcome '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toUnsafeHtml())
            ->toBe('<h2>Welcome <span data-type="mergeTag" data-id="name">John</span></h2>');
    });

    it('renders a string merge tag inline inside a heading via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [
                    ['type' => 'text', 'text' => 'Welcome '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toText())->toBe('Welcome John');
    });

    it('normalizes a stored `rawHtmlMergeTag` back to a `mergeTag` and re-resolves the value via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'rawHtmlMergeTag', 'attrs' => ['id' => 'name'], 'html' => '<b>stale</b>'],
                ]],
            ],
        ])->mergeTags(['name' => 'Fresh']);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p><span data-type="mergeTag" data-id="name">Fresh</span></p>');
    });

    it('normalizes a stored `rawHtmlMergeTag` back to a `mergeTag` and re-resolves the value via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'rawHtmlMergeTag', 'attrs' => ['id' => 'name'], 'html' => '<b>stale</b>'],
                ]],
            ],
        ])->mergeTags(['name' => 'Fresh']);

        expect($renderer->toText())->toBe('Fresh');
    });

    it('resolves a `Closure` returning a string via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'lazy']],
                ]],
            ],
        ])->mergeTags(['lazy' => fn () => 'resolved']);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p><span data-type="mergeTag" data-id="lazy">resolved</span></p>');
    });

    it('resolves a `Closure` returning an `Htmlable` via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'lazy_html']],
                ]],
            ],
        ])->mergeTags(['lazy_html' => fn () => new HtmlString('<b>bold</b>')]);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p><b>bold</b></p>');
    });

    it('resolves a `Closure` returning an `Htmlable` and extracts text via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'lazy_html']],
                ]],
            ],
        ])->mergeTags(['lazy_html' => fn () => new HtmlString('<b>bold</b>')]);

        expect($renderer->toText())->toBe('bold');
    });

    it('renders an empty `Htmlable` merge tag as nothing between surrounding text via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'before'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'empty_html']],
                    ['type' => 'text', 'text' => 'after'],
                ]],
            ],
        ])->mergeTags(['empty_html' => new HtmlString('')]);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p>beforeafter</p>');
    });

    it('renders an empty `Htmlable` merge tag as nothing between surrounding text via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'before'],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'empty_html']],
                    ['type' => 'text', 'text' => 'after'],
                ]],
            ],
        ])->mergeTags(['empty_html' => new HtmlString('')]);

        expect($renderer->toText())->toBe('beforeafter');
    });

    it('renders two adjacent string merge tags without spacing between them via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'first']],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'second']],
                ]],
            ],
        ])->mergeTags(['first' => 'Hello', 'second' => 'World']);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p><span data-type="mergeTag" data-id="first">Hello</span><span data-type="mergeTag" data-id="second">World</span></p>');
    });

    it('renders two adjacent string merge tags without spacing between them via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'first']],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'second']],
                ]],
            ],
        ])->mergeTags(['first' => 'Hello', 'second' => 'World']);

        expect($renderer->toText())->toBe('HelloWorld');
    });

    it('renders two adjacent `Htmlable` merge tags without spacing between them via `toUnsafeHtml()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'first']],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'second']],
                ]],
            ],
        ])->mergeTags([
            'first' => new HtmlString('<b>Hello</b>'),
            'second' => new HtmlString('<em>World</em>'),
        ]);

        expect($renderer->toUnsafeHtml())
            ->toBe('<p><b>Hello</b><em>World</em></p>');
    });

    it('renders a string merge tag touching parentheses without whitespace via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => '('],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                    ['type' => 'text', 'text' => ')'],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toText())->toBe('(John)');
    });

    it('renders an `Htmlable` merge tag touching parentheses without whitespace via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => '('],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                    ['type' => 'text', 'text' => ')'],
                ]],
            ],
        ])->mergeTags(['name' => new HtmlString('<strong>John</strong>')]);

        expect($renderer->toText())->toBe('(John)');
    });

    it('renders a merge tag inline with differently-marked adjacent text via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Hello ', 'marks' => [['type' => 'bold']]],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'name']],
                    ['type' => 'text', 'text' => '!'],
                ]],
            ],
        ])->mergeTags(['name' => 'John']);

        expect($renderer->toText())->toBe('Hello John!');
    });

    it('normalizes whitespace from multi-line `Htmlable` merge tags via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => 'Info: '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'card']],
                ]],
            ],
        ])->mergeTags(['card' => new HtmlString('
            <div class="card">
                <h3>Title</h3>
                <p>Description</p>
            </div>
        ')]);

        expect($renderer->toText())->toBe('Info: Title Description');
    });

    it('normalizes whitespace from block-level `Htmlable` merge tags via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'text', 'text' => '('],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'items']],
                    ['type' => 'text', 'text' => ')'],
                ]],
            ],
        ])->mergeTags(['items' => new HtmlString('<ul><li>Item 1</li><li>Item 2</li></ul>')]);

        expect($renderer->toText())->toBe('(Item 1Item 2)');
    });

    it('renders two adjacent `Htmlable` merge tags without spacing between them via `toText()`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'first']],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'second']],
                ]],
            ],
        ])->mergeTags([
            'first' => new HtmlString('<b>Hello</b>'),
            'second' => new HtmlString('<em>World</em>'),
        ]);

        expect($renderer->toText())->toBe('HelloWorld');
    });
});

describe('mentions', function (): void {
    it('renders mentions as `<span>` elements with `data-type="mention"`', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '1',
                                'label' => 'John Doe',
                                'char' => '@',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<span');
        expect($html)->toContain('data-type="mention"');
        expect($html)->toContain('@John Doe');
    });

    it('hydrates mention labels from `MentionProvider` when label is missing', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '1',
                                'char' => '@',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mentions([
            MentionProvider::make('@')
                ->getLabelsUsing(fn (array $ids): array => ['1' => 'John Doe']),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('@John Doe');
    });

    it('renders mentions as `<a>` elements when `MentionProvider` has a `url()` configured', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '1',
                                'char' => '@',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mentions([
            MentionProvider::make('@')
                ->getLabelsUsing(fn (array $ids): array => ['1' => 'John Doe'])
                ->url(fn (string $id, string $label): string => "/users/{$id}"),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<a');
        expect($html)->toContain('href="/users/1"');
        expect($html)->toContain('data-type="mention"');
        expect($html)->toContain('@John Doe');
    });

    it('batch fetches labels for multiple mentions with the same char', function (): void {
        $fetchedIds = [];

        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '1',
                                'char' => '@',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => ' and ',
                        ],
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '2',
                                'char' => '@',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mentions([
            MentionProvider::make('@')
                ->getLabelsUsing(function (array $ids) use (&$fetchedIds): array {
                    $fetchedIds = $ids;

                    return [
                        '1' => 'John',
                        '2' => 'Jane',
                    ];
                }),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($fetchedIds)->toBe(['1', '2']);
        expect($html)->toContain('@John');
        expect($html)->toContain('@Jane');
    });

    it('handles multiple mention providers with different chars', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '1',
                                'char' => '@',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => ' mentioned ',
                        ],
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '100',
                                'char' => '#',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mentions([
            MentionProvider::make('@')
                ->getLabelsUsing(fn (array $ids): array => ['1' => 'John']),
            MentionProvider::make('#')
                ->getLabelsUsing(fn (array $ids): array => ['100' => 'issue-123']),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('@John');
        expect($html)->toContain('#issue-123');
    });

    it('falls back to existing label when provider returns no label', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '999',
                                'char' => '@',
                                'label' => 'Existing Label',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mentions([
            MentionProvider::make('@')
                ->getLabelsUsing(fn (array $ids): array => []),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('@Existing Label');
    });

    it('falls back to id as label when provider returns no label and no label is set', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '999',
                                'char' => '@',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mentions([
            MentionProvider::make('@')
                ->getLabelsUsing(fn (array $ids): array => []),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('@999');
    });

    it('renders mentions without provider configured', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '1',
                                'label' => 'John',
                                'char' => '@',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('@John');
    });

    it('uses static `items()` for label lookup when `getLabelsUsing()` is not configured', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => 'admin',
                                'char' => '@',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $renderer->mentions([
            MentionProvider::make('@')
                ->items([
                    'admin' => 'Administrator',
                    'user' => 'Regular User',
                ]),
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('@Administrator');
    });

    it('renders mentions without labels as empty spans', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'mention',
                            'attrs' => [
                                'id' => '1',
                                'char' => '@',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<span data-type="mention" data-id="1" data-char="@"></span>');
    });
});

describe('link protocols', function (): void {
    it('preserves links with default protocols', function (): void {
        $renderer = RichContentRenderer::make(
            '<p><a href="https://example.com">Link</a></p>',
        );

        $html = $renderer->toUnsafeHtml();

        expect($html)
            ->toContain('href="https://example.com"')
            ->not->toContain('target=')
            ->not->toContain('rel=');
    });

    it('strips links with unknown protocols by default', function (): void {
        $renderer = RichContentRenderer::make(
            '<p><a href="myapp:///path"><strong>Link</strong></a></p>',
        );

        $html = $renderer->toUnsafeHtml();

        expect($html)->not->toContain('myapp:///path');
    });

    it('preserves links with custom protocols when `linkProtocols()` includes them', function (): void {
        $renderer = RichContentRenderer::make(
            '<p><a href="myapp:///cards?id=123"><strong>Open App</strong></a></p>',
        );

        $renderer->linkProtocols([
            ...RichContentRenderer::make()->getLinkProtocols(),
            'myapp',
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('href="myapp:///cards?id=123"');
    });

    it('uses default protocols from `Link` when `linkProtocols()` is not set', function (): void {
        $renderer = RichContentRenderer::make();

        $protocols = $renderer->getLinkProtocols();

        expect($protocols)
            ->toContain('http')
            ->toContain('https')
            ->toContain('mailto')
            ->toContain('tel');
    });

    it('preserves explicit link attributes during HTML round-trip', function (): void {
        $renderer = RichContentRenderer::make(
            '<p><a href="https://example.com" target="_blank" rel="noopener noreferrer">Link</a></p>',
        );

        $html = $renderer->toUnsafeHtml();

        expect($html)
            ->toContain('href="https://example.com"')
            ->toContain('target="_blank"')
            ->toContain('rel="noopener noreferrer"');
    });
});

// Concrete test blocks for getCustomBlockHtml tests
class RendererTestAlertBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'alert';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div class="alert-' . ($config['type'] ?? 'info') . '">' . ($data['message'] ?? '') . '</div>';
    }
}

class RendererTestBannerBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'banner';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div class="banner">' . ($config['text'] ?? '') . '</div>';
    }
}

describe('`getMergeTagValue()` logic', function (): void {
    it('resolves a merge tag value', function (): void {
        $renderer = RichContentRenderer::make('')
            ->mergeTags(['name' => 'John Doe']);

        expect($renderer->getMergeTagValue('name'))->toBe('John Doe');
    });

    it('resolves a `Closure` merge tag value and caches it', function (): void {
        $callCount = 0;
        $renderer = RichContentRenderer::make('')
            ->mergeTags(['name' => static function () use (&$callCount) {
                $callCount++;

                return 'Computed';
            }]);

        expect($renderer->getMergeTagValue('name'))->toBe('Computed');
        expect($renderer->getMergeTagValue('name'))->toBe('Computed');
        expect($callCount)->toBe(1);
    });

    it('returns `null` for missing merge tag', function (): void {
        $renderer = RichContentRenderer::make('')
            ->mergeTags(['name' => 'John']);

        expect($renderer->getMergeTagValue('missing'))->toBeNull();
    });
});

describe('`getCustomBlockHtml()` logic', function (): void {
    it('finds block by string value (class name)', function (): void {
        $renderer = RichContentRenderer::make('')
            ->customBlocks([RendererTestAlertBlock::class]);

        expect($renderer->getCustomBlockHtml('alert', ['type' => 'warning']))->toBe('<div class="alert-warning"></div>');
    });

    it('finds block by string key with config data', function (): void {
        $renderer = RichContentRenderer::make('')
            ->customBlocks([RendererTestAlertBlock::class => ['message' => 'Hello']]);

        expect($renderer->getCustomBlockHtml('alert', ['type' => 'info']))->toBe('<div class="alert-info">Hello</div>');
    });

    it('returns `null` for non-existent block ID', function (): void {
        $renderer = RichContentRenderer::make('')
            ->customBlocks([RendererTestAlertBlock::class]);

        expect($renderer->getCustomBlockHtml('nonexistent', []))->toBeNull();
    });

    it('finds correct block among multiple blocks', function (): void {
        $renderer = RichContentRenderer::make('')
            ->customBlocks([RendererTestAlertBlock::class, RendererTestBannerBlock::class]);

        expect($renderer->getCustomBlockHtml('banner', ['text' => 'Hi']))->toBe('<div class="banner">Hi</div>');
        expect($renderer->getCustomBlockHtml('alert', ['type' => 'error']))->toBe('<div class="alert-error"></div>');
    });

    it('flattens grouped blocks and finds them by ID', function (): void {
        $renderer = RichContentRenderer::make('')
            ->customBlocks([
                'Alerts' => [RendererTestAlertBlock::class],
                RendererTestBannerBlock::class,
            ]);

        expect($renderer->getCustomBlockHtml('alert', ['type' => 'info']))->toBe('<div class="alert-info"></div>');
        expect($renderer->getCustomBlockHtml('banner', ['text' => 'Hi']))->toBe('<div class="banner">Hi</div>');
    });

    it('preserves data associations alongside groups', function (): void {
        $renderer = RichContentRenderer::make('')
            ->customBlocks([
                RendererTestAlertBlock::class => ['message' => 'With data'],
                'Banners' => [
                    RendererTestBannerBlock::class,
                ],
            ]);

        expect($renderer->getCustomBlockHtml('alert', ['type' => 'warning']))->toBe('<div class="alert-warning">With data</div>');
        expect($renderer->getCustomBlockHtml('banner', ['text' => 'Hi']))->toBe('<div class="banner">Hi</div>');
    });

    it('preserves data associations within groups', function (): void {
        $renderer = RichContentRenderer::make('')
            ->customBlocks([
                'Alerts' => [
                    RendererTestAlertBlock::class => ['message' => 'From group'],
                ],
            ]);

        expect($renderer->getCustomBlockHtml('alert', ['type' => 'warning']))->toBe('<div class="alert-warning">From group</div>');
    });
});

describe('`processCustomBlocks()` end-to-end rendering', function (): void {
    it('renders custom block HTML in output', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'alert',
                        'config' => ['type' => 'warning'],
                        'label' => 'Alert',
                        'preview' => '',
                    ],
                ],
            ],
        ]);

        $renderer->customBlocks([RendererTestAlertBlock::class]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<div class="alert-warning"></div>');
    });

    it('renders custom block with keyed data in output', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'alert',
                        'config' => ['type' => 'info'],
                        'label' => 'Alert',
                        'preview' => '',
                    ],
                ],
            ],
        ]);

        $renderer->customBlocks([RendererTestAlertBlock::class => ['message' => 'Hello World']]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toContain('<div class="alert-info">Hello World</div>');
    });

    it('does not crash when content contains an unknown custom block ID', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'nonexistent',
                        'config' => [],
                        'label' => 'Missing',
                        'preview' => '',
                    ],
                ],
            ],
        ]);

        $renderer->customBlocks([RendererTestAlertBlock::class]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toBeString();
    });

    it('renders multiple custom blocks in content', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'alert',
                        'config' => ['type' => 'error'],
                        'label' => 'Alert',
                        'preview' => '',
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => 'Between blocks'],
                    ],
                ],
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'banner',
                        'config' => ['text' => 'Welcome'],
                        'label' => 'Banner',
                        'preview' => '',
                    ],
                ],
            ],
        ]);

        $renderer->customBlocks([RendererTestAlertBlock::class, RendererTestBannerBlock::class]);

        $html = $renderer->toUnsafeHtml();

        expect($html)
            ->toContain('<div class="alert-error"></div>')
            ->toContain('Between blocks')
            ->toContain('<div class="banner">Welcome</div>');
    });

    it('skips custom blocks when no blocks are registered', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'alert',
                        'config' => ['type' => 'info'],
                        'label' => 'Alert',
                        'preview' => '',
                    ],
                ],
            ],
        ]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toBeString();
    });

    it('skips custom block nodes without an ID', function (): void {
        $renderer = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'config' => [],
                        'label' => 'No ID',
                        'preview' => '',
                    ],
                ],
            ],
        ]);

        $renderer->customBlocks([RendererTestAlertBlock::class]);

        $html = $renderer->toUnsafeHtml();

        expect($html)->toBeString();
    });
});

describe('`getMentionProvider()` logic', function (): void {
    it('returns `null` when no providers are set', function (): void {
        $renderer = RichContentRenderer::make('');

        expect($renderer->getMentionProvider('@'))->toBeNull();
    });

    it('finds provider by matching character', function (): void {
        $atProvider = MentionProvider::make('@');
        $hashProvider = MentionProvider::make('#');

        $renderer = RichContentRenderer::make('')
            ->mentions([$atProvider, $hashProvider]);

        expect($renderer->getMentionProvider('#'))->toBe($hashProvider);
    });

    it('falls back to first provider when no character matches', function (): void {
        $atProvider = MentionProvider::make('@');

        $renderer = RichContentRenderer::make('')
            ->mentions([$atProvider]);

        expect($renderer->getMentionProvider('!'))->toBe($atProvider);
    });
});

describe('`getTextColors()` logic', function (): void {
    it('returns defaults when no colors are set', function (): void {
        $renderer = RichContentRenderer::make('');

        $colors = $renderer->getTextColors();

        expect($colors)->toBeArray()->not->toBeEmpty();
        expect(array_values($colors)[0])->toBeInstanceOf(TextColor::class);
    });

    it('transforms string colors to `TextColor` objects', function (): void {
        $renderer = RichContentRenderer::make('')
            ->textColors(['red' => '#ff0000']);

        expect($renderer->getTextColors()['red'])->toBeInstanceOf(TextColor::class);
    });

    it('passes through `TextColor` objects unchanged', function (): void {
        $tc = TextColor::make('#ff0000', 'red');
        $renderer = RichContentRenderer::make('')
            ->textColors(['red' => $tc]);

        expect($renderer->getTextColors()['red'])->toBe($tc);
    });
});

describe('`toArray()` logic', function (): void {
    it('returns empty array when content is empty', function (): void {
        expect(RichContentRenderer::make('')->toArray())->toBe([]);
    });

    it('returns empty array when content is `null`', function (): void {
        expect(RichContentRenderer::make(null)->toArray())->toBe([]);
    });
});

describe('fluent API', function (): void {
    it('returns fluent `$this` from setters', function (): void {
        $renderer = RichContentRenderer::make('');

        expect($renderer->content(''))->toBe($renderer);
        expect($renderer->fileAttachmentsDisk('public'))->toBe($renderer);
        expect($renderer->fileAttachmentsVisibility('public'))->toBe($renderer);
        expect($renderer->plugins([]))->toBe($renderer);
        expect($renderer->mergeTags(null))->toBe($renderer);
        expect($renderer->customBlocks(null))->toBe($renderer);
        expect($renderer->mentions(null))->toBe($renderer);
        expect($renderer->textColors(null))->toBe($renderer);
        expect($renderer->linkProtocols(null))->toBe($renderer);
    });
});

describe('file attachment URLs', function (): void {
    it('generates a temporary URL for a private file on disk', function (): void {
        Storage::fake('attachments');
        Storage::disk('attachments')->put('photos/avatar.jpg', 'binary');

        $renderer = RichContentRenderer::make('')
            ->fileAttachmentsDisk('attachments')
            ->fileAttachmentsVisibility('private');

        $url = $renderer->getFileAttachmentUrl('photos/avatar.jpg');

        expect($url)->toBeString();
        // Temporary URLs include a signature parameter under the `Storage::fake` driver.
        expect($url)->toContain('photos/avatar.jpg');
    });

    it('generates a public URL for a file on a public disk', function (): void {
        Storage::fake('public-attachments');
        Storage::disk('public-attachments')->put('photos/logo.jpg', 'binary');

        $renderer = RichContentRenderer::make('')
            ->fileAttachmentsDisk('public-attachments')
            ->fileAttachmentsVisibility('public');

        $url = $renderer->getFileAttachmentUrl('photos/logo.jpg');

        expect($url)->toBeString();
        expect($url)->toContain('photos/logo.jpg');
    });

    it('returns `null` when the file does not exist on the configured disk', function (): void {
        Storage::fake('attachments');

        $renderer = RichContentRenderer::make('')
            ->fileAttachmentsDisk('attachments')
            ->fileAttachmentsVisibility('public');

        expect($renderer->getFileAttachmentUrl('does-not-exist.jpg'))->toBeNull();
    });

    it('delegates URL generation to a configured `FileAttachmentProvider`', function (): void {
        $provider = new class implements FileAttachmentProvider
        {
            public function getFileAttachmentUrl(mixed $file): ?string
            {
                return "provider://{$file}";
            }

            public function saveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
            {
                return null;
            }

            public function getDefaultFileAttachmentVisibility(): ?string
            {
                return 'private';
            }

            public function isExistingRecordRequiredToSaveNewFileAttachments(): bool
            {
                return false;
            }

            public function cleanUpFileAttachments(array $exceptIds): void {}

            public function attribute(RichContentAttribute $attribute): static
            {
                return $this;
            }
        };

        $renderer = RichContentRenderer::make('')
            ->fileAttachmentProvider($provider);

        expect($renderer->getFileAttachmentUrl('any-id'))->toBe('provider://any-id');
    });

    it('prefers a `FileAttachmentProvider` from a plugin over the default disk lookup', function (): void {
        $plugin = new class implements HasFileAttachmentProvider, RichContentPlugin
        {
            public function getFileAttachmentProvider(): ?FileAttachmentProvider
            {
                return new class implements FileAttachmentProvider
                {
                    public function getFileAttachmentUrl(mixed $file): ?string
                    {
                        return "plugin://{$file}";
                    }

                    public function saveUploadedFileAttachment(TemporaryUploadedFile $file): mixed
                    {
                        return null;
                    }

                    public function getDefaultFileAttachmentVisibility(): ?string
                    {
                        return null;
                    }

                    public function isExistingRecordRequiredToSaveNewFileAttachments(): bool
                    {
                        return false;
                    }

                    public function cleanUpFileAttachments(array $exceptIds): void {}

                    public function attribute(RichContentAttribute $attribute): static
                    {
                        return $this;
                    }
                };
            }

            public function getTipTapPhpExtensions(): array
            {
                return [];
            }

            public function getTipTapJsExtensions(): array
            {
                return [];
            }

            public function getEditorTools(): array
            {
                return [];
            }

            public function getEditorActions(): array
            {
                return [];
            }
        };

        $renderer = RichContentRenderer::make('')->plugins([$plugin]);

        expect($renderer->getFileAttachmentUrl('x'))->toBe('plugin://x');
    });

    it('replaces image `src` with the resolved file attachment URL during `toHtml()`', function (): void {
        Storage::fake('attachments');
        Storage::disk('attachments')->put('photos/a.jpg', 'binary');

        $html = RichContentRenderer::make([
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'image',
                            'attrs' => [
                                'id' => 'photos/a.jpg',
                                'src' => 'placeholder',
                            ],
                        ],
                    ],
                ],
            ],
        ])
            ->fileAttachmentsDisk('attachments')
            ->fileAttachmentsVisibility('public')
            ->toHtml();

        expect($html)->toContain('photos/a.jpg');
        expect($html)->not->toContain('src="placeholder"');
    });
});

describe('output sanitization', function (): void {
    it('strips `<script>` tags from the rendered HTML via `sanitizeHtml`', function (): void {
        $html = RichContentRenderer::make('<p>Hello</p><script>alert(1)</script>')->toHtml();

        expect($html)->toContain('Hello');
        expect($html)->not->toContain('<script');
    });

    it('keeps safe inline formatting marks through the sanitizer', function (): void {
        $html = RichContentRenderer::make('<p><strong>bold</strong> <em>italic</em></p>')->toHtml();

        expect($html)->toContain('<strong>bold</strong>');
        expect($html)->toContain('<em>italic</em>');
    });
});
