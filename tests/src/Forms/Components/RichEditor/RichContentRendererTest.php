<?php

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
