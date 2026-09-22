<?php

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\actingAs;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-breadcrumbs-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([
            Js::make($id, $file->getPathname())->loadedOnRequest(),
        ], 'tests/breadcrumbs');
    }

    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('renders `Breadcrumbs` like Blade and preserves native links during reactive updates', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/breadcrumbs-browser-test');
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->assertScript("document.querySelectorAll('[data-breadcrumbs-row={$framework}] [data-ready]').length", 5);
        }

        $page->assertScript(<<<'JS'
        (() => {
            const tree = (element) => ({
                tag: element.tagName,
                attributes: [...element.attributes].map(({name, value}) => [name, name === 'class' ? value.split(/\s+/).sort().join(' ') : value]).sort(([first], [second]) => first.localeCompare(second)),
                children: [...element.children].map(tree),
                text: element.children.length ? '' : element.textContent.trim(),
            })
            const trees = (framework) => [...document.querySelectorAll(`[data-breadcrumbs-row=${framework}] nav`)].map(tree)
            return ['react', 'vue', 'svelte'].every(framework => JSON.stringify(trees(framework)) === JSON.stringify(trees('blade')))
        })()
        JS, true)->assertNoAccessibilityIssues();

        foreach (['ltr' => 'rtl', 'rtl' => 'ltr'] as $documentDirection => $componentDirection) {
            $page->script("document.documentElement.dir = '{$documentDirection}'; document.querySelectorAll('[data-breadcrumbs-row] nav').forEach(navigation => navigation.dir = '{$componentDirection}')");
            $page->assertScript(<<<JS
            [...document.querySelectorAll('[data-breadcrumbs-row] nav')].every(navigation =>
                [...navigation.querySelectorAll('.fi-{$documentDirection}')].every(icon => getComputedStyle(icon).display === 'none') &&
                [...navigation.querySelectorAll('.fi-{$componentDirection}')].every(icon => getComputedStyle(icon).display !== 'none'),
            )
            JS, true);
        }
        $page->script("document.querySelectorAll('[data-breadcrumbs-row] nav').forEach(navigation => navigation.removeAttribute('dir'))");

        $page->script("document.documentElement.dir = 'rtl'");
        $page->assertScript(<<<'JS'
        [...document.querySelectorAll('[data-breadcrumbs-row] nav')].every(navigation =>
            [...navigation.querySelectorAll('.fi-ltr')].every(icon => getComputedStyle(icon).display === 'none') &&
            [...navigation.querySelectorAll('.fi-rtl')].every(icon => getComputedStyle(icon).display !== 'none'),
        )
        JS, true)->assertNoAccessibilityIssues();

        $page->script("document.documentElement.dir = 'ltr'");

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->click("[data-breadcrumbs-row={$framework}] > div:first-child a[href='#home']")
                ->assertScript('location.hash', '#home')
                ->assertScript("document.querySelector('[data-breadcrumbs-row={$framework}] > div').dataset.clicked", '#home');
            $page->keys("[data-breadcrumbs-row={$framework}] > div:first-child a[href='#users']", 'Enter')
                ->assertScript('location.hash', '#users');
            $page->script("history.replaceState(null, '', location.pathname)");
            $page->click("[data-testid=update-{$framework}]")
                ->assertScript(<<<JS
                (() => {
                    const navigation = document.querySelector('[data-breadcrumbs-row={$framework}] nav')
                    const items = [...navigation.querySelectorAll('li')].map(item => item.lastElementChild)
                    return navigation.getAttribute('aria-label') === 'Updated location'
                        && navigation.dir === 'rtl'
                        && [...navigation.querySelectorAll('.fi-ltr')].every(icon => getComputedStyle(icon).display === 'none')
                        && [...navigation.querySelectorAll('.fi-rtl')].every(icon => getComputedStyle(icon).display !== 'none')
                        && navigation.className === 'fi-breadcrumbs custom-breadcrumbs'
                        && !navigation.hasAttribute('title')
                        && items.length === 3
                        && JSON.stringify(items.map(item => item.textContent.trim())) === JSON.stringify(['Updated home', 'Former link', 'New current'])
                        && items[0].getAttribute('href') === '#updated'
                        && items[1].tagName === 'SPAN' && !items[1].hasAttribute('href')
                        && items[2].tagName === 'A' && items[2].getAttribute('href') === '#current'
                        && items[2].target === '_blank' && items[2].rel === 'noopener'
                        && items[2].getAttribute('aria-current') === 'page'
                        && navigation.querySelectorAll('[aria-current]').length === 1
                })()
                JS, true)
                ->click("[data-breadcrumbs-row={$framework}] a[href='#updated']")
                ->assertScript('location.hash', '')
                ->assertScript("document.querySelector('[data-breadcrumbs-row={$framework}] > div').dataset.clicked", '#updated');
        }

        $page->assertNoSmoke()->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->click("[data-testid=reset-{$framework}]")
                ->assertScript("(() => { const navigation = document.querySelector('[data-breadcrumbs-row={$framework}] nav'); return navigation.querySelectorAll('li').length === 0 && navigation.getAttribute('aria-label') === 'Breadcrumbs' && navigation.className === 'fi-breadcrumbs' && !navigation.hasAttribute('dir') })()", true);
        }

        $page->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
