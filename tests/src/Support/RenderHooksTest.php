<?php

use Filament\Support\Facades\FilamentView;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

test('render hooks can be registered', function (): void {
    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    });

    expect(FilamentView::renderHook('foo'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('bar');
});

test('render hooks can render view files', function (): void {
    FilamentView::registerRenderHook('view-foo', function (): View {
        return view('pages.render-hooks.foo');
    });

    expect(FilamentView::renderHook('view-foo'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toContain('bar');
});

test('render hooks can be scopes:d', function (): void {
    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    });

    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    }, 'baz');

    expect(FilamentView::renderHook('foo', scopes: 'baz'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('barbar');
});

test('render hooks can be scopes:d to multiple scopes:s', function (): void {
    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    });

    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    }, ['baz', 'qux']);

    expect(FilamentView::renderHook('foo', scopes: 'baz'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('barbar');

    expect(FilamentView::renderHook('foo', scopes: 'qux'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('barbar');
});

test('render hooks can be scopes:d to multiple scopes:s but only ever output once', function (): void {
    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    });

    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    }, ['baz', 'qux']);

    expect(FilamentView::renderHook('foo', scopes: ['baz', 'qux']))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('barbar');
});
