<?php

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Theme;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $theme = Theme::make('admin', '/path/to/admin.css');

    expect($theme)->toBeInstanceOf(Theme::class);
    expect($theme)->toBeInstanceOf(Css::class);
});

it('inherits all `Css` functionality', function (): void {
    $theme = Theme::make('admin')
        ->package('my-package');

    $path = $theme->getRelativePublicPath();

    expect($path)->toContain('css/my-package/admin.css');
});

it('generates a `<link>` tag from `getHtml()`', function (): void {
    $theme = Theme::make('admin')
        ->package('my-package');

    $html = $theme->getHtml()->toHtml();

    expect($html)->toContain('<link');
    expect($html)->toContain('rel="stylesheet"');
});
