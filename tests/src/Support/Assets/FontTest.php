<?php

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Font;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $font = Font::make('inter', '/path/to/inter');

    expect($font)->toBeInstanceOf(Font::class);
    expect($font->getId())->toBe('inter');
});

it('generates a relative public path containing the font ID', function (): void {
    $font = Font::make('inter')
        ->package('my-package');

    $path = $font->getRelativePublicPath();

    expect($path)->toContain('fonts/my-package/inter');
});

it('returns a `Css` asset from `getStyle()`', function (): void {
    $font = Font::make('inter', '/path/to/inter')
        ->package('my-package');

    $style = $font->getStyle();

    expect($style)->toBeInstanceOf(Css::class);
    expect($style->getId())->toBe('inter');
});

it('appends `index.css` to the style path', function (): void {
    $font = Font::make('inter', '/path/to/inter')
        ->package('my-package');

    $style = $font->getStyle();

    expect($style->getRelativePublicPath())->toContain('index.css');
});
