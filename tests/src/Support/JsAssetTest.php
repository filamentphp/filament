<?php

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\TestCase;

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
