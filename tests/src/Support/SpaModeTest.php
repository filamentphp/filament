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

test('SPA prefetching can be toggled', function (): void {
    expect(FilamentView::hasSpaPrefetching())->toBeFalse();

    FilamentView::spa(true, true);
    expect(FilamentView::hasSpaPrefetching())->toBeTrue();

    FilamentView::spa(true, false);
    expect(FilamentView::hasSpaPrefetching())->toBeFalse();
});

test('`href` HTML can be generated with `wire:navigate` based on SPA mode', function (): void {
    FilamentView::spa();
    expect(generate_href_html('http://localhost/page'))
        ->toHtml()->toBe('href="http://localhost/page" wire:navigate');

    FilamentView::spa(false);
    expect(generate_href_html('http://localhost/page'))
        ->toHtml()->toBe('href="http://localhost/page"');
});

test('`href` HTML can be generated with `wire:navigate.hover` when prefetching is enabled', function (): void {
    FilamentView::spa(true, true);
    expect(generate_href_html('http://localhost/page'))
        ->toHtml()->toBe('href="http://localhost/page" wire:navigate.hover');

    FilamentView::spa(true, false);
    expect(generate_href_html('http://localhost/page'))
        ->toHtml()->toBe('href="http://localhost/page" wire:navigate');
});

test('`wire:navigate` is not used in the `href` HTML if it doesn\'t match the request\'s domain', function (): void {
    FilamentView::spa();
    expect(generate_href_html('http://another-localhost/page'))
        ->toHtml()->toBe('href="http://another-localhost/page"');

    FilamentView::spa(false);
    expect(generate_href_html('http://another-localhost/page'))
        ->toHtml()->toBe('href="http://another-localhost/page"');
});

test('`wire:navigate.hover` is not used for external URLs even when prefetching is enabled', function (): void {
    FilamentView::spa(true, true);
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

test('`target` HTML takes precedence over SPA prefetching', function (): void {
    FilamentView::spa(true, true);
    expect(generate_href_html('http://localhost/page', shouldOpenInNewTab: true))
        ->toHtml()->toBe('href="http://localhost/page" target="_blank"');
});

test('SPA URL exceptions work correctly', function (): void {
    FilamentView::spa();
    FilamentView::spaUrlExceptions(['*/admin/*']);

    expect(FilamentView::hasSpaMode('http://localhost/admin/users'))->toBeFalse();
    expect(FilamentView::hasSpaMode('http://localhost/dashboard'))->toBeTrue();
});

test('`href` HTML respects SPA URL exceptions for prefetching', function (): void {
    FilamentView::spa(true, true);
    FilamentView::spaUrlExceptions(['*/admin/*']);

    expect(generate_href_html('http://localhost/admin/users'))
        ->toHtml()->toBe('href="http://localhost/admin/users"');

    expect(generate_href_html('http://localhost/dashboard'))
        ->toHtml()->toBe('href="http://localhost/dashboard" wire:navigate.hover');
});

test('`shouldOpenInSpaMode` parameter overrides default SPA mode behavior', function (): void {
    FilamentView::spa(false);

    expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: true))
        ->toHtml()->toBe('href="http://localhost/page" wire:navigate');

    expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: false))
        ->toHtml()->toBe('href="http://localhost/page"');
});

test('`shouldOpenInSpaMode` parameter works with prefetching', function (): void {
    FilamentView::spa(true, true);

    expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: true))
        ->toHtml()->toBe('href="http://localhost/page" wire:navigate.hover');

    expect(generate_href_html('http://localhost/page', shouldOpenInSpaMode: false))
        ->toHtml()->toBe('href="http://localhost/page"');
});

test('blank URL returns empty HTML string', function (): void {
    expect(generate_href_html(''))->toHtml()->toBe('');
    expect(generate_href_html(null))->toHtml()->toBe('');
});

test('SPA URL exceptions can be chained', function (): void {
    FilamentView::spa();
    FilamentView::spaUrlExceptions(['*/admin/*']);
    FilamentView::spaUrlExceptions(['*/api/*']);

    expect(FilamentView::hasSpaMode('http://localhost/admin/users'))->toBeFalse();
    expect(FilamentView::hasSpaMode('http://localhost/api/data'))->toBeFalse();
    expect(FilamentView::hasSpaMode('http://localhost/dashboard'))->toBeTrue();
});

test('SPA URL exceptions work with complex patterns', function (): void {
    FilamentView::spa();
    FilamentView::spaUrlExceptions([
        '*/admin/users/*',
        '*/api/v1/*',
        '*/public/*',
    ]);

    expect(FilamentView::hasSpaMode('http://localhost/admin/users/123'))->toBeFalse();
    expect(FilamentView::hasSpaMode('http://localhost/api/v1/users'))->toBeFalse();
    expect(FilamentView::hasSpaMode('http://localhost/public/files'))->toBeFalse();
    expect(FilamentView::hasSpaMode('http://localhost/admin/dashboard'))->toBeTrue();
    expect(FilamentView::hasSpaMode('http://localhost/api/v2/users'))->toBeTrue();
});

test('SPA mode works with different URL formats', function (): void {
    FilamentView::spa();

    $root = request()->root();

    expect(FilamentView::hasSpaMode($root . '/page'))->toBeTrue();
    expect(FilamentView::hasSpaMode($root . '/page?param=value'))->toBeTrue();
    expect(FilamentView::hasSpaMode($root . '/page#fragment'))->toBeTrue();
    expect(FilamentView::hasSpaMode($root . '/'))->toBeTrue();
    expect(FilamentView::hasSpaMode($root))->toBeTrue();
});

test('SPA prefetching works with different URL formats', function (): void {
    FilamentView::spa(true, true);

    $root = request()->root();

    expect(FilamentView::hasSpaPrefetching($root . '/page'))->toBeTrue();
    expect(FilamentView::hasSpaPrefetching($root . '/page?param=value'))->toBeTrue();
    expect(FilamentView::hasSpaPrefetching($root . '/page#fragment'))->toBeTrue();
    expect(FilamentView::hasSpaPrefetching($root . '/'))->toBeTrue();
    expect(FilamentView::hasSpaPrefetching($root))->toBeTrue();
});

test('SPA mode handles edge cases gracefully', function (): void {
    FilamentView::spa();

    expect(FilamentView::hasSpaMode('http://localhost'))->toBeTrue();
    expect(FilamentView::hasSpaMode('http://localhost/'))->toBeTrue();
    expect(FilamentView::hasSpaMode('http://localhost///'))->toBeTrue();
    expect(FilamentView::hasSpaMode('http://localhost/page with spaces'))->toBeTrue();
    expect(FilamentView::hasSpaMode('http://localhost/page%20with%20encoding'))->toBeTrue();
});

test('SPA prefetching handles edge cases gracefully', function (): void {
    FilamentView::spa(true, true);

    expect(FilamentView::hasSpaPrefetching('http://localhost'))->toBeTrue();
    expect(FilamentView::hasSpaPrefetching('http://localhost/'))->toBeTrue();
    expect(FilamentView::hasSpaPrefetching('http://localhost///'))->toBeTrue();
    expect(FilamentView::hasSpaPrefetching('http://localhost/page with spaces'))->toBeTrue();
    expect(FilamentView::hasSpaPrefetching('http://localhost/page%20with%20encoding'))->toBeTrue();
});
