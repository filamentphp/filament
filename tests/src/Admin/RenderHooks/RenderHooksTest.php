<?php

use Filament\Facades\Filament;
use Filament\Tests\Admin\RenderHooks\TestCase;
use Illuminate\Support\Facades\Blade;

uses(TestCase::class);

test('render hooks can be registered', function () {
    Filament::registerRenderHook('foo', function () {
        return Blade::render('foobar');
    });

    expect(Filament::renderHook('foo'))
        ->toBeString()
        ->toBe('foobar');
});

test('render hooks can render view files', function () {
    Filament::registerRenderHook('view-foo', function () {
        return view('fixtures.pages.render-hooks.foo');
    });

    expect(Filament::renderHook('view-foo'))
        ->toBeString()
        ->toContain('Hello, foo!');
});
