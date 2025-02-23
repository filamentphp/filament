<?php

use Filament\Support\Facades\FilamentView;
use Filament\Tests\TestCase;

use function Filament\Support\generate_href_html;

uses(TestCase::class);

test('SPA mode can be toggled', function (): void {
    expect(FilamentView::hasSpaMode())->toBeFalse();

    FilamentView::spa();
    expect(FilamentView::hasSpaMode())->toBeTrue();

    FilamentView::spa(false);
    expect(FilamentView::hasSpaMode())->toBeFalse();
});

test('`href` HTML can be generated with `wire:navigate` based on SPA mode', function (): void {
    FilamentView::spa();
    expect(generate_href_html('http://localhost/page'))
        ->toHtml()->toBe('href="http://localhost/page" wire:navigate');

    FilamentView::spa(false);
    expect(generate_href_html('http://localhost/page'))
        ->toHtml()->toBe('href="http://localhost/page"');
});

test('`wire:navigate` is not used in the `href` HTML if it doesn\'t match the request\'s domain', function (): void {
    FilamentView::spa();
    expect(generate_href_html('http://another-localhost/page'))
        ->toHtml()->toBe('href="http://another-localhost/page"');

    FilamentView::spa(false);
    expect(generate_href_html('http://another-localhost/page'))
        ->toHtml()->toBe('href="http://another-localhost/page"');
});

test('`target` HTML can be generated if the URL should open in a new tab', function (): void {
    FilamentView::spa();
    expect(generate_href_html('http://localhost/page', shouldOpenInNewTab: true))
        ->toHtml()->toBe('href="http://localhost/page" target="_blank"');

    FilamentView::spa(false);
    expect(generate_href_html('http://localhost/page', shouldOpenInNewTab: true))
        ->toHtml()->toBe('href="http://localhost/page" target="_blank"');
});
