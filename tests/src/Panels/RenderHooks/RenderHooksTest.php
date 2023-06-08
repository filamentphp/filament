<?php

use Filament\Facades\Filament;
use Filament\Tests\Panels\RenderHooks\TestCase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

test('render hooks can be registered', function () {
    Filament::getCurrentPanel()->renderHook('foo', function (): string {
        return Blade::render('bar');
    });

    expect(Filament::renderHook('foo'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('bar');
});

test('render hooks can render view files', function () {
    Filament::getCurrentPanel()->renderHook('view-foo', function (): Illuminate\Contracts\View\View {
        return view('app.fixtures.pages.render-hooks.foo');
    });

    expect(Filament::renderHook('view-foo'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toContain('bar');
});
