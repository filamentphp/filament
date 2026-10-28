<?php

use Filament\Support\Facades\FilamentView;
use Filament\Support\View\ViewManager;
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

test('render hooks can be scoped to multiple scopes but only ever output once', function (): void {
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

test('mobile menu hook spellings share scopes, ordering and callback identity', function (string $legacy, string $canonical): void {
    foreach ([[$legacy, $canonical], [$canonical, $legacy]] as [$first, $second]) {
        FilamentView::swap(new ViewManager);

        foreach ([$first, $second] as $name) {
            expect(FilamentView::hasRenderHook($name))->toBeFalse()
                ->and(FilamentView::renderHook($name)->toHtml())->toBe('');
        }

        FilamentView::registerRenderHook($first, static fn (): string => 'A', scopes: 'alpha');
        FilamentView::registerRenderHook($second, static fn (): string => 'B', scopes: 'beta');

        foreach ([$first, $second] as $name) {
            expect(FilamentView::hasRenderHook($name))->toBeFalse()
                ->and(FilamentView::hasRenderHook($name, scopes: 'unrelated'))->toBeFalse()
                ->and(FilamentView::hasRenderHook($name, scopes: 'alpha'))->toBeTrue()
                ->and(FilamentView::hasRenderHook($name, scopes: ['unrelated', 'beta']))->toBeTrue()
                ->and(FilamentView::renderHook($name, scopes: ['beta', 'alpha'])->toHtml())->toBe('BA');
        }

        $calls = 0;
        $sharedHook = function (array $scopes, array $data) use (&$calls): HtmlString {
            $calls++;

            return new HtmlString('<b>' . implode(',', $scopes) . ':' . $data['value'] . '</b>');
        };

        FilamentView::registerRenderHook($first, static fn (): string => '1');
        FilamentView::registerRenderHook($second, $sharedHook, scopes: ['alpha', 'beta']);
        FilamentView::registerRenderHook($second, static fn (): string => '2');
        FilamentView::registerRenderHook($first, $sharedHook);
        FilamentView::registerRenderHook($second, $sharedHook);
        FilamentView::registerRenderHook($first, static fn (): string => 'R');
        FilamentView::registerRenderHook($second, static fn (): string => 'R');

        foreach ([$first, $second] as $name) {
            $calls = 0;

            expect(FilamentView::hasRenderHook($name))->toBeTrue()
                ->and(FilamentView::hasRenderHook($name, scopes: 'unrelated'))->toBeTrue()
                ->and(FilamentView::renderHook($name, scopes: ['beta', 'alpha'], data: ['value' => 'payload'])->toHtml())
                ->toBe('12<b>beta,alpha:payload</b>RRBA')
                ->and($calls)->toBe(1);
        }
    }
})->with([
    ['panels::page.sub-navigation.module-menu.before', 'panels::page.sub-navigation.mobile-menu.before'],
    ['panels::page.sub-navigation.module-menu.after', 'panels::page.sub-navigation.mobile-menu.after'],
]);

test('unknown render hook spellings remain independent', function (): void {
    FilamentView::registerRenderHook('panels::page.sub-navigation.module-menu.custom', static fn (): string => 'legacy');

    expect(FilamentView::hasRenderHook('panels::page.sub-navigation.mobile-menu.custom'))->toBeFalse()
        ->and(FilamentView::renderHook('panels::page.sub-navigation.mobile-menu.custom')->toHtml())->toBe('');

    FilamentView::registerRenderHook('panels::page.sub-navigation.mobile-menu.custom', static fn (): string => 'canonical');

    expect(FilamentView::renderHook('panels::page.sub-navigation.module-menu.custom')->toHtml())->toBe('legacy')
        ->and(FilamentView::renderHook('panels::page.sub-navigation.mobile-menu.custom')->toHtml())->toBe('canonical');
});
