<?php

use Filament\FontProviders\GoogleFontProvider;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;

uses(TestCase::class);

it('returns `Htmlable` from `getHtml()`', function (): void {
    $provider = new GoogleFontProvider;

    $html = $provider->getHtml('Inter');

    expect($html)->toBeInstanceOf(Htmlable::class);
});

it('includes the Google fonts URL', function (): void {
    $provider = new GoogleFontProvider;

    $html = $provider->getHtml('Inter');

    expect($html->toHtml())->toContain('fonts.googleapis.com');
});

it('uses a custom URL when provided', function (): void {
    $provider = new GoogleFontProvider;

    $html = $provider->getHtml('Inter', 'https://custom.url/font.css');

    expect($html->toHtml())->toContain('https://custom.url/font.css');
});
