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

test('render hooks can be scoped', function (): void {
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

test('render hooks can be scoped to multiple scoped', function (): void {
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

test('render hooks can be scoped to multiple scoped but only ever output once', function (): void {
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

test('render hooks can be passed data', function (): void {
    FilamentView::registerRenderHook('foo', function ($data): string {
        return $data['foo'];
    });

    expect(FilamentView::renderHook('foo', data: ['foo' => 'bar']))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('bar');
});

it('renders an empty string for unregistered names and unmatched scopes with `renderHook()`', function (): void {
    FilamentView::registerRenderHook('registered', static fn (): string => 'scoped', 'matching');

    expect(FilamentView::renderHook('missing', ['matching'])->toHtml())->toBe('')
        ->and(FilamentView::renderHook('registered', ['different'])->toHtml())->toBe('');
});

it('preserves global and scope order while deduplicating closure identities with `renderHook()`', function (): void {
    $calls = [];
    $shared = static function (array $scopes, array $data) use (&$calls): string {
        $calls[] = [$scopes, $data];

        return 'shared';
    };

    FilamentView::registerRenderHook('ordered', $shared, ['', 'first', 'second']);
    FilamentView::registerRenderHook('ordered', static fn (): string => 'global');
    FilamentView::registerRenderHook('ordered', static fn (): string => 'first', 'first');
    FilamentView::registerRenderHook('ordered', static fn (): string => 'second', 'second');

    $scopes = ['second', 'first', 'second', ''];
    expect(FilamentView::renderHook('ordered', $scopes, ['value' => 42])->toHtml())->toBe('sharedglobalsecondfirst')
        ->and($calls)->toBe([[$scopes, ['value' => 42]]]);
});

it('observes registrations in later scopes without changing the current scope snapshot in `renderHook()`', function (): void {
    FilamentView::registerRenderHook('changing', static function (): string {
        FilamentView::registerRenderHook('changing', static fn (): string => 'new-global');
        FilamentView::registerRenderHook('changing', static fn (): string => 'new-scoped', 'later');

        return 'original';
    });

    expect(FilamentView::renderHook('changing', ['later'])->toHtml())->toBe('originalnew-scoped');
});

it('executes hooks again and propagates exceptions through `renderHook()`', function (): void {
    $calls = 0;
    FilamentView::registerRenderHook('live', static function () use (&$calls): string {
        return (string) ++$calls;
    });

    expect(FilamentView::renderHook('live')->toHtml())->toBe('1')
        ->and(FilamentView::renderHook('live')->toHtml())->toBe('2');

    FilamentView::registerRenderHook('throwing', static fn () => throw new RuntimeException('hook failure'));

    expect(static fn () => FilamentView::renderHook('throwing'))->toThrow(RuntimeException::class, 'hook failure');
});
