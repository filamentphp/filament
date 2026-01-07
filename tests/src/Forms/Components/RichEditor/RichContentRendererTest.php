<?php

use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

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

it('preserves existing labels and does not refetch them', function (): void {
    $fetchCount = 0;

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
                            'label' => 'Existing Label',
                            'char' => '@',
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $renderer->mentions([
        MentionProvider::make('@')
            ->getLabelsUsing(function (array $ids) use (&$fetchCount): array {
                $fetchCount++;

                return ['1' => 'New Label'];
            }),
    ]);

    $html = $renderer->toUnsafeHtml();

    expect($fetchCount)->toBe(0);
    expect($html)->toContain('@Existing Label');
    expect($html)->not->toContain('New Label');
});

it('falls back to id as label when provider returns no label', function (): void {
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
