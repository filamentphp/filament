<?php

use Filament\Forms\Components\RichEditor\TipTapExtensions\RawHtmlMergeTagExtension;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('has correct extension name', function (): void {
    expect(RawHtmlMergeTagExtension::$name)->toBe('rawHtmlMergeTag');
});

it('renders HTML content without escaping', function (): void {
    $extension = new RawHtmlMergeTagExtension;

    $node = (object) [
        'html' => '<strong>Bold text</strong> with <em>emphasis</em>',
    ];

    $result = $extension->renderHTML($node);

    expect($result)->toBe(['content' => '<strong>Bold text</strong> with <em>emphasis</em>']);
});

it('renders complex HTML structures', function (): void {
    $extension = new RawHtmlMergeTagExtension;

    $node = (object) [
        'html' => '<div class="test"><p>Paragraph with <a href="https://example.com">link</a></p><ul><li>Item 1</li><li>Item 2</li></ul></div>',
    ];

    $result = $extension->renderHTML($node);

    expect($result)->toBe(['content' => '<div class="test"><p>Paragraph with <a href="https://example.com">link</a></p><ul><li>Item 1</li><li>Item 2</li></ul></div>']);
});

it('handles empty HTML content', function (): void {
    $extension = new RawHtmlMergeTagExtension;

    $node = (object) [
        'html' => '',
    ];

    $result = $extension->renderHTML($node);

    expect($result)->toBe(['content' => '']);
});

it('handles null HTML content', function (): void {
    $extension = new RawHtmlMergeTagExtension;

    $node = (object) [
        'html' => null,
    ];

    $result = $extension->renderHTML($node);

    expect($result)->toBe(['content' => null]);
});
