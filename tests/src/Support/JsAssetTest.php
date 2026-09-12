<?php

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Vite;

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
        Vite::useCspNonce('abc123');

        FilamentAsset::register([
            Js::make('test-js-nonced', 'test.js'),
        ], 'test-package');

        expect(FilamentAsset::renderScripts(['test-package']))
            ->toContain('nonce="abc123"');
    });

    it('uses the same `Vite` nonce as Livewire', function (): void {
        Vite::useCspNonce('abc123');

        FilamentAsset::register([
            Js::make('test-js-nonced', 'test.js'),
        ], 'test-package');

        expect(FilamentAsset::renderScripts(['test-package']))
            ->toContain('nonce="abc123"')
            ->and(Blade::render('@livewireScripts'))
            ->toContain('nonce="abc123"');
    });

    it('renders the same `nonce` on registered scripts and script data', function (): void {
        Vite::useCspNonce('abc123');

        FilamentAsset::register([
            Js::make('test-js-one', 'one.js'),
            Js::make('test-js-two', 'two.js'),
        ], 'test-package');

        expect(substr_count(FilamentAsset::renderScripts(['test-package']), 'nonce="abc123"'))
            ->toBe(3);
    });

    it('escapes the `nonce` attribute', function (): void {
        Vite::useCspNonce('abc"><script>alert(1)</script>');

        FilamentAsset::register([
            Js::make('test-js-nonced', 'test.js'),
        ], 'test-package');

        expect(FilamentAsset::renderScripts(['test-package']))
            ->not->toContain('<script>alert(1)</script>')
            ->toContain('nonce="abc&quot;&gt;&lt;script&gt;alert(1)&lt;/script&gt;"');
    });
});
