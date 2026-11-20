<?php

use Filament\Support\Facades\FilamentView;
use Filament\Tests\TestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

describe('SPA mode', function (): void {
    it('can enable and disable `spa()`', function (): void {
        expect(FilamentView::hasSpaMode())->toBeFalse();

        FilamentView::spa();
        expect(FilamentView::hasSpaMode())->toBeTrue();

        FilamentView::spa(false);
        expect(FilamentView::hasSpaMode())->toBeFalse();
    });

    it('can enable and disable prefetching with `spa()`', function (): void {
        expect(FilamentView::hasSpaPrefetching())->toBeFalse();

        FilamentView::spa(true, true);
        expect(FilamentView::hasSpaPrefetching())->toBeTrue();

        FilamentView::spa(true, false);
        expect(FilamentView::hasSpaPrefetching())->toBeFalse();
    });

    it('can exclude URLs using `spaUrlExceptions()`', function (): void {
        FilamentView::spa();
        FilamentView::spaUrlExceptions(['*/admin/*']);

        expect(FilamentView::hasSpaMode('http://localhost/admin/users'))->toBeFalse();
        expect(FilamentView::hasSpaMode('http://localhost/dashboard'))->toBeTrue();
    });

    it('can chain `spaUrlExceptions()` registrations', function (): void {
        FilamentView::spa();
        FilamentView::spaUrlExceptions(['*/admin/*']);
        FilamentView::spaUrlExceptions(['*/api/*']);

        expect(FilamentView::hasSpaMode('http://localhost/admin/users'))->toBeFalse();
        expect(FilamentView::hasSpaMode('http://localhost/api/data'))->toBeFalse();
        expect(FilamentView::hasSpaMode('http://localhost/dashboard'))->toBeTrue();
    });

    it('can match complex `spaUrlExceptions()` patterns', function (): void {
        FilamentView::spa();
        FilamentView::spaUrlExceptions([
            '*/admin/users/*',
            '*/api/v1/*',
            '*/public/*',
        ]);

        expect(FilamentView::hasSpaMode('http://localhost/admin/users/123'))->toBeFalse();
        expect(FilamentView::hasSpaMode('http://localhost/api/v1/users'))->toBeFalse();
        expect(FilamentView::hasSpaMode('http://localhost/public/files'))->toBeFalse();
        expect(FilamentView::hasSpaMode('http://localhost/admin/dashboard'))->toBeTrue();
        expect(FilamentView::hasSpaMode('http://localhost/api/v2/users'))->toBeTrue();
    });

    it('supports different URL formats with `hasSpaMode()`', function (): void {
        FilamentView::spa();

        $root = request()->root();

        expect(FilamentView::hasSpaMode($root . '/page'))->toBeTrue();
        expect(FilamentView::hasSpaMode($root . '/page?param=value'))->toBeTrue();
        expect(FilamentView::hasSpaMode($root . '/page#fragment'))->toBeTrue();
        expect(FilamentView::hasSpaMode($root . '/'))->toBeTrue();
        expect(FilamentView::hasSpaMode($root))->toBeTrue();
    });

    it('supports root URLs and encoded paths with `hasSpaMode()`', function (): void {
        FilamentView::spa();

        expect(FilamentView::hasSpaMode('http://localhost'))->toBeTrue();
        expect(FilamentView::hasSpaMode('http://localhost/'))->toBeTrue();
        expect(FilamentView::hasSpaMode('http://localhost///'))->toBeTrue();
        expect(FilamentView::hasSpaMode('http://localhost/page with spaces'))->toBeTrue();
        expect(FilamentView::hasSpaMode('http://localhost/page%20with%20encoding'))->toBeTrue();
    });
});

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

test('render hooks can be scoped to multiple scopes', function (): void {
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

test('render hooks output once per registration across matching scopes', function (array $scopes): void {
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
    'global and scoped' => [['', 'baz', 'qux']],
]);

test('stateless render hook registrations remain independent across matching scopes', function (): void {
    foreach (range(1, 2) as $registration) {
        FilamentView::registerRenderHook('independent', static fn (array $data): string => $data['content'], scopes: ['alpha', 'beta']);
    }

    expect(FilamentView::renderHook('independent', scopes: ['beta', 'alpha', 'beta'], data: ['content' => 'hook'])->toHtml())
        ->toBe('hookhook');
});

test('reusing a render hook preserves its closure-local static state', function (): void {
    $hook = static function (): string {
        static $count = 0;

        return (string) ++$count;
    };

    FilamentView::registerRenderHook('stateful', $hook);
    FilamentView::registerRenderHook('stateful', $hook);

    expect(FilamentView::renderHook('stateful')->toHtml())->toBe('12')
        ->and(FilamentView::renderHook('stateful')->toHtml())->toBe('34');
});

test('render hooks can be passed data', function (): void {
    FilamentView::registerRenderHook('foo', function ($data): string {
        return $data['foo'];
    });

    expect(FilamentView::renderHook('foo', data: ['foo' => 'bar']))
        ->toBeInstanceOf(HtmlString::class)
        ->toHtml()->toBe('bar');
});
