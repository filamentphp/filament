<?php

use Filament\Facades\Filament;
use Filament\Tests\Admin\RenderHooks\TestCase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

test('render hooks can be registered', function () {
    Filament::registerRenderHook('foo', function () {
        return Blade::render('bar');
    });

    expect(Filament::renderHook('foo'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('bar');
});

test('render hooks can render view files', function () {
    Filament::registerRenderHook('view-foo', function () {
        return view('fixtures.pages.render-hooks.foo');
    });

    expect(Filament::renderHook('view-foo'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toContain('Hello, foo!');
});
