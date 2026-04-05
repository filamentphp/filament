<?php

use Filament\Support\Markdown;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can convert a block of markdown to a string of HTML', function (): void {
    $markdown = new Markdown('This is a **snippet** of _example_ Markdown.');

    expect($markdown->toHtml())
        ->toBe("<p>This is a <strong>snippet</strong> of <em>example</em> Markdown.</p>\n");
});

it('can convert inline markdown to a string of HTML', function (): void {
    $markdown = new Markdown('This is a **snippet** of _example_ Markdown.', isInline: true);

    expect($markdown->toHtml())
        ->toBe("This is a <strong>snippet</strong> of <em>example</em> Markdown.\n");
});

it('can create a block `Markdown` instance via `block()`', function (): void {
    $markdown = Markdown::block('Hello **world**.');

    expect($markdown->toHtml())
        ->toBe("<p>Hello <strong>world</strong>.</p>\n");
});

it('can create an inline `Markdown` instance via `inline()`', function (): void {
    $markdown = Markdown::inline('Hello **world**.');

    expect($markdown->toHtml())
        ->toBe("Hello <strong>world</strong>.\n");
});

it('can cast a block `Markdown` instance to string via `__toString()`', function (): void {
    $markdown = Markdown::block('Hello **world**.');

    expect((string) $markdown)
        ->toBe("<p>Hello <strong>world</strong>.</p>\n");
});

it('can cast an inline `Markdown` instance to string via `__toString()`', function (): void {
    $markdown = Markdown::inline('Hello **world**.');

    expect((string) $markdown)
        ->toBe("Hello <strong>world</strong>.\n");
});
