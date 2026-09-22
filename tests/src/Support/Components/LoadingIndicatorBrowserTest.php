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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-loading-indicator-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/indicators');
    }

    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('renders `LoadingIndicator` like Blade and preserves reactive SVG attributes and events', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/loading-indicator-browser-test', ['reducedMotion' => $theme === 'dark' ? 'reduce' : 'no-preference']);

        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }

        $page->assertScript('document.documentElement.classList.contains("dark")', $theme === 'dark');

        $page->assertScript("document.querySelectorAll('[data-indicator-row=react] [data-svg-ref]').length", 7)
            ->assertScript(<<<'JS'
            (() => {
                const normalize = (element) => ({
                    tag: element.tagName,
                    namespace: element.namespaceURI,
                    attributes: [...element.attributes].map(({ name, value }) => [name, name === 'class' ? value.split(/\s+/).filter(Boolean).sort().join(' ') : value]).sort(([first], [second]) => first.localeCompare(second)),
                    children: [...element.children].map(normalize),
                })
                const indicators = (framework) => [...document.querySelectorAll(`[data-indicator-row=${framework}] svg`)].map(normalize)
                return ['react', 'vue', 'svelte'].every(framework => JSON.stringify(indicators(framework)) === JSON.stringify(indicators('blade')))
            })()
            JS, true)
            ->assertNoAccessibilityIssues();

        $page->assertScript("matchMedia('(prefers-reduced-motion: reduce)').matches", $theme === 'dark')
            ->assertScript("[...document.querySelectorAll('[data-indicator-row] svg')].every(element => getComputedStyle(element).animationName === '" . ($theme === 'dark' ? 'none' : 'spin') . "')", true);

        $page->script("window.indicatorElements = [...document.querySelectorAll('[data-indicator-row] svg')]");
        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->script("document.querySelector('[data-indicator-row={$framework}] svg').dispatchEvent(new MouseEvent('click', { bubbles: true }))");
            $page->assertScript("document.querySelectorAll('[data-indicator-row={$framework}] [data-clicked]').length", 1)
                ->click("[data-testid=update-{$framework}]")
                ->assertScript("[...document.querySelectorAll('[data-indicator-row={$framework}] svg')].every(element => element.getAttribute('class') === 'fi-icon fi-loading-indicator fi-size-xl custom-indicator' && element.getAttribute('viewBox') === '0 0 32 32' && element.getAttribute('fill') === 'red' && element.getAttribute('aria-hidden') === 'false' && element.dataset.state === 'updated')", true);
        }
        $page->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->click("[data-testid=reset-{$framework}]")
                ->assertScript("[...document.querySelectorAll('[data-indicator-row={$framework}] svg')].every(element => element.getAttribute('class') === 'fi-icon fi-loading-indicator fi-size-md' && element.getAttribute('viewBox') === '0 0 24 24' && element.getAttribute('fill') === 'none' && element.getAttribute('aria-hidden') === 'true' && !element.hasAttribute('data-state') && !element.hasAttribute('role') && !element.hasAttribute('aria-label'))", true);
        }
        $page->assertScript("[...document.querySelectorAll('[data-indicator-row] svg')].every((element, index) => element === window.indicatorElements[index])", true)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});
