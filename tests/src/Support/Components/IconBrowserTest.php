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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-icon-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/icons');
    }

    Artisan::call('filament:assets');
    File::put(public_path('icon-browser-test.svg'), '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke="black" d="M4 12h16"/></svg>');
    actingAs(User::factory()->create());
});

afterEach(function (): void {
    File::delete(public_path('icon-browser-test.svg'));
});

it('renders `Icon` like Blade and updates content, sizes, native attributes, events and refs', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/icon-browser-test');

        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }

        $parity = <<<'JS'
        (() => {
            const normalize = (element) => ({
                tag: element.tagName,
                namespace: element.namespaceURI,
                attributes: [...element.attributes].map(({ name, value }) => [name, name === 'class' ? value.split(/\s+/).filter(Boolean).sort().join(' ') : value]).sort(([first], [second]) => first.localeCompare(second)),
                children: [...element.children].map(normalize),
            })
            const icons = (framework) => [...document.querySelectorAll(`[data-icon-row=${framework}] > div`)].map(container => container.firstElementChild ? normalize(container.firstElementChild) : null)
            return ['react', 'vue', 'svelte'].every(framework => JSON.stringify(icons(framework)) === JSON.stringify(icons('blade')))
        })()
        JS;

        $page->assertScript("document.querySelectorAll('[data-icon-row=react] [data-ref=SPAN]').length", 7)
            ->assertScript($parity, true)
            ->assertScript(<<<'JS'
            (() => {
                const widths = [20, 12, 16, 20, 24, 28, 32];
                return ['blade', 'react', 'vue', 'svelte'].every(framework => [...document.querySelectorAll(`[data-icon-row=${framework}] svg`)].every((element, index) => element.getBoundingClientRect().width === widths[index]))
            })()
            JS, true)
            ->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->script("document.querySelector('[data-icon-row={$framework}] svg').dispatchEvent(new MouseEvent('click', { bubbles: true }))");
            $page->assertScript("document.querySelector('[data-icon-row={$framework}] > div').dataset.clicked", 'SPAN')
                ->click("[data-testid=update-{$framework}]")
                ->assertScript("[...document.querySelectorAll('[data-icon-row={$framework}] > div > span')].filter(element => element.dataset.state === 'updated' && element.getAttribute('aria-label') === 'Saved' && element.querySelector('svg').getBoundingClientRect().width === 28 && element.querySelector('path').getAttribute('d') === 'M6 6l12 12M6 18L18 6').length", 10);
        }
        $page->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->click("[data-testid=image-{$framework}]")
                ->assertScript("[...document.querySelectorAll('[data-icon-row={$framework}] img')].filter(element => element.alt === 'Saved image' && element.getAttribute('loading') === 'eager' && !element.hasAttribute('data-state') && !element.hasAttribute('aria-label') && element.getBoundingClientRect().width === 16).length", 10);
            $page->script("document.querySelector('[data-icon-row={$framework}] img').dispatchEvent(new MouseEvent('click', { bubbles: true }))");
            $page->assertScript("document.querySelector('[data-icon-row={$framework}] > div').dataset.clicked", 'IMG');
        }
        $page->assertScript("document.querySelectorAll('[data-icon-row=react] [data-ref=IMG]').length", 10)
            ->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->click("[data-testid=reset-{$framework}]");
        }
        $page->assertScript($parity, true)
            ->assertScript("[...document.querySelectorAll('[data-icon-row=react] > div')].map(element => element.dataset.ref).join(',')", 'SPAN,SPAN,SPAN,SPAN,SPAN,SPAN,SPAN,IMG,IMG,none')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});
