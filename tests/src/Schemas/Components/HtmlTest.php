<?php

use Filament\Schemas\Components\Html;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

it('can be constructed with a string', function (): void {
    $html = Html::make('<p>Hello</p>');

    expect($html->getContent())->toBe('<p>Hello</p>');
});

it('can be constructed with `null`', function (): void {
    $html = Html::make(null);

    expect($html->getContent())->toBeNull();
});

it('can set `content()` with a `Closure`', function (): void {
    $html = Html::make(null)
        ->content(static fn (): string => '<strong>Dynamic</strong>');

    expect($html->getContent())->toBe('<strong>Dynamic</strong>');
});

it('can set `content()` with an `Htmlable`', function (): void {
    $htmlable = new HtmlString('<em>Rich</em>');
    $html = Html::make($htmlable);

    expect($html->getContent())->toBe($htmlable);
});

describe('`toEmbeddedHtml()` logic', function (): void {
    it('returns string content as-is', function (): void {
        $html = Html::make('<p>Test</p>');

        expect($html->toEmbeddedHtml())->toBe('<p>Test</p>');
    });

    it('calls `toHtml()` on `Htmlable` content', function (): void {
        $html = Html::make(new HtmlString('<div>Rich</div>'));

        expect($html->toEmbeddedHtml())->toBe('<div>Rich</div>');
    });

    it('returns empty string when content is `null`', function (): void {
        $html = Html::make(null);

        expect($html->toEmbeddedHtml())->toBe('');
    });

    it('renders `content()` set via `Closure`', function (): void {
        $html = Html::make(null)
            ->content(static fn (): string => '<strong>Dynamic</strong>');

        expect($html->toEmbeddedHtml())->toBe('<strong>Dynamic</strong>');
    });
});
