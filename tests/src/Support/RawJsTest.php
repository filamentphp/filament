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

    // RawJs stores the JS as-is, unlike Js::from() which JSON-encodes
    expect((string) $rawJs)->toBe('document.title');
});

it('does not JSON-encode strings like `Js::from()` would', function (): void {
    $rawJs = RawJs::make("alert('hello')");

    // Js::from("alert('hello')") would produce a quoted JSON string
    // RawJs keeps it as raw JS
    expect((string) $rawJs)->toBe("alert('hello')");
});

it('preserves complex expressions', function (): void {
    $rawJs = RawJs::make('() => { return $wire.data.name }');

    expect((string) $rawJs)->toBe('() => { return $wire.data.name }');
});
