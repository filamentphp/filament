<?php

use Filament\Support\Assets\Js;
use Filament\Support\Csp\CspManager;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('registers script tag with type of module', function (): void {
    FilamentAsset::register([
        Js::make('test-js', 'test.js'),
    ]);

    expect(FilamentAsset::renderScripts())
        ->not->toContain('type="module"');

    FilamentAsset::register([
        Js::make('test-js-with-module', 'test.js')->module(),
    ]);

    expect(FilamentAsset::renderScripts())
        ->toContain('type="module"');
});

describe('CSP nonce', function (): void {
    it('does not render a `nonce` attribute by default', function (): void {
        FilamentAsset::register([
            Js::make('test-js-plain', 'test.js'),
        ]);

        expect(FilamentAsset::renderScripts())
            ->not->toContain('nonce=');
    });

    it('renders a `nonce` attribute on script tags when one is configured', function (): void {
        app(CspManager::class)->useNonce('abc123');

        FilamentAsset::register([
            Js::make('test-js-nonced', 'test.js'),
        ]);

        expect(FilamentAsset::renderScripts())
            ->toContain('nonce="abc123"');
    });

    it('renders the same `nonce` on every script tag', function (): void {
        app(CspManager::class)->useNonce('abc123');

        FilamentAsset::register([
            Js::make('test-js-one', 'one.js'),
            Js::make('test-js-two', 'two.js'),
        ]);

        expect(substr_count(FilamentAsset::renderScripts(), 'nonce="abc123"'))
            ->toBeGreaterThanOrEqual(2);
    });
});
