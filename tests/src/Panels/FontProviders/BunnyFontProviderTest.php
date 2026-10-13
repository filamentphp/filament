<?php

use Filament\FontProviders\BunnyFontProvider;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;

uses(TestCase::class);

it('returns `Htmlable` from `getHtml()`', function (): void {
    $provider = new BunnyFontProvider;

    $html = $provider->getHtml('Inter');

    expect($html)->toBeInstanceOf(Htmlable::class);
});

it('includes the Bunny fonts URL', function (): void {
    $provider = new BunnyFontProvider;

    $html = $provider->getHtml('Inter');

    expect($html->toHtml())->toContain('fonts.bunny.net');
});

it('uses a custom URL when provided', function (): void {
    $provider = new BunnyFontProvider;

    $html = $provider->getHtml('Inter', 'https://custom.url/font.css');

    expect($html->toHtml())->toContain('https://custom.url/font.css');
});

it('formats the family name to kebab-case', function (): void {
    $provider = new BunnyFontProvider;

    $html = $provider->getHtml('Open Sans');

    expect($html->toHtml())->toContain('open-sans');
});
