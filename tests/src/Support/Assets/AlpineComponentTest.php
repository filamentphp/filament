<?php

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $asset = AlpineComponent::make('my-component');

    expect($asset)->toBeInstanceOf(AlpineComponent::class);
    expect($asset)->toBeInstanceOf(Asset::class);
});

describe('public path', function (): void {
    it('returns a path containing the component ID', function (): void {
        $asset = AlpineComponent::make('date-picker')
            ->package('filament/forms');

        $path = $asset->getRelativePublicPath();

        expect($path)->toContain('date-picker.js');
        expect($path)->toContain('components');
        expect($path)->toContain('filament/forms');
    });

    it('returns a `getPublicPath()` that starts with the public path', function (): void {
        $asset = AlpineComponent::make('date-picker')
            ->package('filament/forms');

        $publicPath = $asset->getPublicPath();

        expect($publicPath)->toContain('date-picker.js');
    });

    it('returns a `getSrc()` URL with a version query string', function (): void {
        $asset = AlpineComponent::make('date-picker')
            ->package('filament/forms');

        $src = $asset->getSrc();

        expect($src)->toContain('date-picker.js');
        expect($src)->toContain('?v=');
    });
});
