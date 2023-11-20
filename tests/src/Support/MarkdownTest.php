<?php

use Filament\Support\Markdown;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can convert a block of markdown to a string of HTML', function () {
    $markdown = new Markdown('This is a **snippet** of _example_ Markdown.');

    expect($markdown->toHtml())
        ->toBe("<p>This is a <strong>snippet</strong> of <em>example</em> Markdown.</p>\n");
});

it('can convert inline markdown to a string of HTML', function () {
    $markdown = new Markdown('This is a **snippet** of _example_ Markdown.', isInline: true);

    expect($markdown->toHtml())
        ->toBe("This is a <strong>snippet</strong> of <em>example</em> Markdown.\n");
});
