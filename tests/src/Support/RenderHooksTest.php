<?php

use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

test('render hooks can be registered', function () {
    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    });

    expect(FilamentView::renderHook('foo'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('bar');
});

test('render hooks can render view files', function () {
    FilamentView::registerRenderHook('view-foo', function (): View {
        return view('app.fixtures.pages.render-hooks.foo');
    });

    expect(FilamentView::renderHook('view-foo'))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toContain('bar');
});

test('render hooks can be scopes:d', function () {
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

test('render hooks can be scopes:d to multiple scopes:s', function () {
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

test('render hooks output once per registration across matching scopes', function (array $scopes) {
    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    });

    FilamentView::registerRenderHook('foo', function (): string {
        return Blade::render('bar');
    }, $scopes);

    expect(FilamentView::renderHook('foo', scopes: ['qux', 'baz', 'qux']))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('barbar');
})->with([
    'multiple scopes' => [['baz', 'qux']],
    'repeated scopes' => [['baz', 'qux', 'baz']],
    'global and scoped' => [[null, 'baz', 'qux']],
]);

test('stateless render hook registrations remain independent across matching scopes', function (array $scopes, bool $isPanel) {
    $panel = $isPanel ? Panel::make()->id('independent-hooks') : null;
    $registerHook = $panel ? $panel->renderHook(...) : FilamentView::registerRenderHook(...);

    foreach (range(1, 2) as $registration) {
        $registerHook('independent', static fn (): string => 'hook', scopes: $scopes);
    }

    $panel?->boot();

    expect(FilamentView::renderHook('independent', scopes: ['resource', 'page', 'resource'])->toHtml())
        ->toBe('hookhook');
})->with([
    'multiple scopes' => [['page', 'resource']],
    'global and repeated scopes' => [[null, 'page', 'resource', 'page']],
])->with([
    'direct' => false,
    'panel' => true,
]);

test('reusing a render hook preserves its closure-local static state', function (bool $isPanel) {
    $panel = $isPanel ? Panel::make()->id('stateful-hooks') : null;
    $registerHook = $panel ? $panel->renderHook(...) : FilamentView::registerRenderHook(...);
    $hook = static function (): string {
        static $count = 0;

        return (string) ++$count;
    };

    $registerHook('stateful', $hook);
    $registerHook('stateful', $hook);

    $panel?->boot();

    expect(FilamentView::renderHook('stateful')->toHtml())->toBe('12')
        ->and(FilamentView::renderHook('stateful')->toHtml())->toBe('34');
})->with([
    'direct' => false,
    'panel' => true,
]);

test('render hooks preserve global-first ordering, requested scope ordering and scope injection', function (bool $isPanel) {
    $panel = $isPanel ? Panel::make()->id('ordered-hooks') : null;
    $registerHook = $panel ? $panel->renderHook(...) : FilamentView::registerRenderHook(...);

    $registerHook('ordered', static fn (): string => 'A', scopes: 'alpha');
    $registerHook('ordered', static fn (): string => 'G');
    $registerHook('ordered', static fn (array $scopes): string => '[' . implode(',', $scopes) . ']', scopes: ['alpha', 'beta']);
    $registerHook('ordered', static fn (): string => 'B', scopes: 'beta');

    $panel?->boot();

    expect(FilamentView::renderHook('ordered', scopes: ['beta', 'alpha'])->toHtml())->toBe('G[beta,alpha]BA');
})->with([
    'direct' => false,
    'panel' => true,
]);
