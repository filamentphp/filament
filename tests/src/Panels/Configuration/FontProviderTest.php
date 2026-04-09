<?php

use Filament\FontProviders\BunnyFontProvider;
use Filament\FontProviders\GoogleFontProvider;
use Filament\FontProviders\LocalFontProvider;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;

uses(TestCase::class);

describe('BunnyFontProvider', function (): void {
    it('returns `Htmlable` from `getHtml()`', function (): void {
        $provider = new BunnyFontProvider;

        $html = $provider->getHtml('Inter');

        expect($html)->toBeInstanceOf(Htmlable::class);
    });

    it('includes Bunny fonts URL', function (): void {
        $provider = new BunnyFontProvider;

        $html = $provider->getHtml('Inter');

        expect($html->toHtml())->toContain('fonts.bunny.net');
    });

    it('uses custom URL when provided', function (): void {
        $provider = new BunnyFontProvider;

        $html = $provider->getHtml('Inter', 'https://custom.url/font.css');

        expect($html->toHtml())->toContain('https://custom.url/font.css');
    });

    it('formats family name to kebab-case', function (): void {
        $provider = new BunnyFontProvider;

        $html = $provider->getHtml('Open Sans');

        expect($html->toHtml())->toContain('open-sans');
    });
});

describe('GoogleFontProvider', function (): void {
    it('returns `Htmlable` from `getHtml()`', function (): void {
        $provider = new GoogleFontProvider;

        $html = $provider->getHtml('Inter');

        expect($html)->toBeInstanceOf(Htmlable::class);
    });

    it('includes Google fonts URL', function (): void {
        $provider = new GoogleFontProvider;

        $html = $provider->getHtml('Inter');

        expect($html->toHtml())->toContain('fonts.googleapis.com');
    });

    it('uses custom URL when provided', function (): void {
        $provider = new GoogleFontProvider;

        $html = $provider->getHtml('Inter', 'https://custom.url/font.css');

        expect($html->toHtml())->toContain('https://custom.url/font.css');
    });
});

describe('LocalFontProvider', function (): void {
    it('returns `Htmlable` from `getHtml()`', function (): void {
        $provider = new LocalFontProvider;

        $html = $provider->getHtml('Inter');

        expect($html)->toBeInstanceOf(Htmlable::class);
    });

    it('uses custom URL when provided', function (): void {
        $provider = new LocalFontProvider;

        $html = $provider->getHtml('Inter', '/fonts/inter.css');

        expect($html->toHtml())->toContain('/fonts/inter.css');
    });
});
