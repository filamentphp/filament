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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-input-wrapper-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/input-wrappers');
    }

    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('renders `InputWrapper` like Blade and reactively composes affixes without changing native input semantics', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/input-wrapper-browser-test');
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }
        $parity = <<<'JS'
    (() => {
        const normalize = (element) => ({
            tag: element.tagName,
            attributes: [...element.attributes].map(({ name, value }) => [name, name === 'class' ? value.split(/\s+/).filter(Boolean).sort().join(' ') : name === 'required' ? '' : value]).sort(([first], [second]) => first.localeCompare(second)),
            text: element.children.length ? null : element.textContent.trim(),
            children: [...element.children].map(normalize),
        })
        const wrappers = (framework) => [...document.querySelectorAll(`[data-wrapper-row=${framework}] > div > div`)].map(normalize)
        return wrappers('blade').length === 8 && ['react', 'vue', 'svelte'].every(framework => JSON.stringify(wrappers(framework)) === JSON.stringify(wrappers('blade')))
    })()
    JS;

        $page->assertScript($parity, true)->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->script("window.wrapperInput = document.querySelector('[data-wrapper-row={$framework}] input')");
            $page->assertScript("document.querySelectorAll('[data-wrapper-row={$framework}] [data-ref=DIV]').length", 8)
                ->fill("[data-wrapper-row={$framework}] > div:first-child input", '125')
                ->click("[data-testid=update-{$framework}]")
                ->assertScript("document.querySelector('[data-wrapper-row={$framework}] input') === window.wrapperInput && window.wrapperInput.value === '125'", true)
                ->assertScript("document.querySelectorAll('[data-wrapper-row={$framework}] [data-state=changed]').length", 8)
                ->assertScript(<<<JS
            [...document.querySelectorAll('[data-wrapper-row={$framework}] [data-state=changed]')].every(element => {
                const prefix = element.firstElementChild;
                const suffix = element.lastElementChild;
                const input = element.querySelector('input');
                return prefix.children[0].querySelector('button').type === 'button'
                    && prefix.children[1].querySelector('svg') !== null
                    && prefix.children[2].querySelector('strong').textContent === 'Price'
                    && suffix.children[0].textContent === 'GBP'
                    && suffix.children[1].querySelector('svg') !== null
                    && suffix.children[2].querySelector('button').type === 'button'
                    && element.classList.contains('fi-disabled') && element.classList.contains('fi-invalid')
                    && !prefix.classList.contains('fi-inline') && suffix.classList.contains('fi-inline')
                    && !input.disabled && !input.hasAttribute('aria-invalid') && input.required;
            })
            JS, true)
                ->assertNoAccessibilityIssues();
            $page->click("[data-testid=currency-{$framework}]")
                ->assertScript("[...document.querySelectorAll('[data-wrapper-row={$framework}] .fi-input-wrp-suffix .fi-input-wrp-label')].every(element => element.textContent === 'USD')", true);
            $page->script("document.querySelector('[data-wrapper-row={$framework}] button').click()");
            $page->assertScript("document.querySelector('[data-wrapper-row={$framework}] > div').dataset.clicked", 'true')
                ->assertScript("document.querySelector('[data-wrapper-row={$framework}] > div').dataset.wrapperClicked", 'DIV')
                ->assertScript("document.querySelector('[data-wrapper-row={$framework}] input').value", '125')
                ->click("[data-testid=clear-{$framework}]")
                ->assertScript("document.querySelector('[data-wrapper-row={$framework}] input') === window.wrapperInput && window.wrapperInput.value === '125'", true)
                ->assertScript("[...document.querySelectorAll('[data-wrapper-row={$framework}] > div > div')].every(element => element.children.length === 1 && !element.hasAttribute('data-state') && element.querySelector('input').required)", true)
                ->click("[data-testid=reset-{$framework}]")
                ->assertScript("document.querySelector('[data-wrapper-row={$framework}] input') === window.wrapperInput && window.wrapperInput.value === '125'", true);
        }

        $page->assertScript($parity, true)->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
