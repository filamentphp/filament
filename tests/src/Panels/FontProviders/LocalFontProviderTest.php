<?php

use Filament\FontProviders\LocalFontProvider;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;

uses(TestCase::class);

it('returns `Htmlable` from `getHtml()`', function (): void {
    $provider = new LocalFontProvider;

    $html = $provider->getHtml('Inter');

    expect($html)->toBeInstanceOf(Htmlable::class);
});

it('uses a custom URL when provided', function (): void {
    $provider = new LocalFontProvider;

    $html = $provider->getHtml('Inter', '/fonts/inter.css');

    expect($html->toHtml())->toContain('/fonts/inter.css');
});
