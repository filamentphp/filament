<?php

use Filament\FontProviders\Contracts\FontProvider;
use Filament\FontProviders\SpatieGoogleFontProvider;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;

uses(TestCase::class);

it('implements `FontProvider`', function (): void {
    $provider = new SpatieGoogleFontProvider;

    expect($provider)->toBeInstanceOf(FontProvider::class);
});

it('returns an `Htmlable` from `getHtml()`', function (): void {
    $provider = new SpatieGoogleFontProvider;

    $html = $provider->getHtml('Inter');

    expect($html)->toBeInstanceOf(Htmlable::class);
});

it('returns an `Htmlable` from `getHtml()` when URL is provided', function (): void {
    $provider = new SpatieGoogleFontProvider;

    $html = $provider->getHtml('Inter', 'https://fonts.googleapis.com/css2?family=Inter');

    expect($html)->toBeInstanceOf(Htmlable::class);
});

it('renders the `@googlefonts` Blade directive output', function (): void {
    $provider = new SpatieGoogleFontProvider;

    $html = $provider->getHtml('Inter');

    // The directive renders a link tag or inline CSS for Google Fonts
    expect($html->toHtml())->toBeString();
});
