<?php

use Composer\InstalledVersions;
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

it('preserves the package identity and version in the `Css` asset from `getStyle()`', function (): void {
    $originalInstalledVersions = InstalledVersions::getAllRawData()[0];

    InstalledVersions::reload([
        'root' => $originalInstalledVersions['root'],
        'versions' => [
            ...$originalInstalledVersions['versions'],
            'vendor/font-package' => [
                'pretty_version' => 'v2.3.1',
                'version' => '2.3.1.0',
                'reference' => 'fedcba0987654321fedcba0987654321fedcba09',
                'type' => 'library',
                'install_path' => __DIR__,
                'aliases' => [],
                'dev_requirement' => false,
            ],
        ],
    ]);

    try {
        $style = Font::make('inter', '/path/to/inter')
            ->package('vendor/font-package')
            ->getStyle();

        expect($style->getPackage())->toBe('vendor/font-package')
            ->and($style->getVersion())->toBe('2.3.1.0');
    } finally {
        InstalledVersions::reload($originalInstalledVersions);
    }
});

it('appends `index.css` to the style path', function (): void {
    $font = Font::make('inter', '/path/to/inter')
        ->package('my-package');

    $style = $font->getStyle();

    expect($style->getRelativePublicPath())->toContain('index.css');
});
