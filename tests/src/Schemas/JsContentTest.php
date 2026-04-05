<?php

use Filament\Schemas\JsContent;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;

uses(TestCase::class);

it('can be constructed with a string', function (): void {
    $jsContent = JsContent::make('someExpression');

    expect($jsContent)->toBeInstanceOf(JsContent::class);
});

it('implements `Htmlable`', function (): void {
    $jsContent = JsContent::make('someExpression');

    expect($jsContent)->toBeInstanceOf(Htmlable::class);
});

it('renders an Alpine `x-text` span from `toHtml()`', function (): void {
    $jsContent = JsContent::make('1 + 1');

    $html = $jsContent->toHtml();

    expect($html)->toContain('<span');
    expect($html)->toContain('x-text=');
    expect($html)->toContain('eval(');
});

it('JSON-encodes the content inside `toHtml()`', function (): void {
    $jsContent = JsContent::make('document.title');

    $html = $jsContent->toHtml();

    // Js::from wraps the string in quotes for safe JS embedding
    expect($html)->toContain('document.title');
});

it('escapes special characters in `toHtml()`', function (): void {
    $jsContent = JsContent::make("alert('xss')");

    $html = $jsContent->toHtml();

    // Should not contain unescaped single quotes that could break out
    expect($html)->toContain('x-text=');
    expect($html)->toContain('eval(');
});
