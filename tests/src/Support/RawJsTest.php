<?php

use Filament\Support\RawJs;
use Filament\Tests\TestCase;
use Illuminate\Support\Js;

uses(TestCase::class);

it('extends `Js`', function (): void {
    $rawJs = RawJs::make('console.log("hello")');

    expect($rawJs)->toBeInstanceOf(Js::class);
});

it('can be constructed with `make()`', function (): void {
    $rawJs = RawJs::make('1 + 1');

    expect($rawJs)->toBeInstanceOf(RawJs::class);
});

it('stores raw JavaScript without encoding', function (): void {
    $rawJs = RawJs::make('document.title');

    expect((string) $rawJs)->toBe('document.title');
});

it('does not JSON-encode strings like `Js::from()` would', function (): void {
    $rawJs = RawJs::make("alert('hello')");

    expect((string) $rawJs)->toBe("alert('hello')");
});

it('preserves complex expressions', function (): void {
    $rawJs = RawJs::make('() => { return $wire.data.name }');

    expect((string) $rawJs)->toBe('() => { return $wire.data.name }');
});
