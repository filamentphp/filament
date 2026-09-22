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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-fieldset-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/fieldset');
    }

    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('renders `Fieldset` like Blade and updates native attributes, legends and children reactively', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/fieldset-browser-test');
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->assertScript("document.querySelectorAll('[data-fieldset-row={$framework}] [data-ready=true]').length", 7);
        }

        $page->assertScript(<<<'JS'
        (() => {
            const tree = (element) => ({
                tag: element.tagName,
                attributes: [...element.attributes].map(({name, value}) => [name, name === 'class' ? value.split(/\s+/).sort().join(' ') : name === 'disabled' ? '' : value]).sort(([first], [second]) => first.localeCompare(second)),
                children: [...element.children].map(tree),
                text: [...element.childNodes].filter(node => node.nodeType === Node.TEXT_NODE).map(node => node.textContent.trim()).join(''),
            })
            const trees = (framework) => [...document.querySelectorAll(`[data-fieldset-row=${framework}] fieldset`)].map(tree)
            return ['react', 'vue', 'svelte'].every(framework => JSON.stringify(trees(framework)) === JSON.stringify(trees('blade')))
        })()
        JS, true)->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $input = "[data-fieldset-row={$framework}] > div:first-child input";
            $page->script("document.querySelector('[data-fieldset-row={$framework}]').originalInput = document.querySelector('{$input}')");
            $page->fill($input, 'Baker Street')
                ->assertScript("document.querySelector('[data-fieldset-row={$framework}] > div').dataset.input", 'Baker Street')
                ->assertScript("document.querySelector('[data-fieldset-row={$framework}] > div').dataset.eventTarget", 'FIELDSET')
                ->click("[data-testid=update-{$framework}]")
                ->assertScript(<<<JS
                (() => {
                    const fieldset = document.querySelector('[data-fieldset-row={$framework}] fieldset')
                    return fieldset.disabled && fieldset.querySelector('input').matches(':disabled')
                        && fieldset.name === 'updated' && fieldset.form.id === 'profile'
                        && !fieldset.hasAttribute('title') && !fieldset.hasAttribute('required')
                        && fieldset.className === 'fi-fieldset fi-fieldset-label-hidden fi-fieldset-not-contained custom-fieldset'
                        && fieldset.querySelector('legend').textContent.trim() === 'Updated address*'
                        && fieldset.querySelector('sup').textContent === '*'
                        && fieldset.querySelector('input').value === 'Baker Street'
                })()
                JS, true);
        }

        $page->assertNoSmoke()->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $page->click("[data-testid=reset-{$framework}]")
                ->assertScript(<<<JS
                (() => {
                    const fieldset = document.querySelector('[data-fieldset-row={$framework}] fieldset')
                    return !fieldset.disabled && !fieldset.querySelector('input').matches(':disabled')
                        && !fieldset.hasAttribute('name') && !fieldset.hasAttribute('form')
                        && !fieldset.querySelector('legend') && fieldset.className === 'fi-fieldset'
                        && fieldset.querySelector('input').value === 'Baker Street'
                })()
                JS, true)
                ->fill("[data-fieldset-row={$framework}] > div:first-child input", 'Updated street')
                ->assertScript("document.querySelector('[data-fieldset-row={$framework}] > div').dataset.input", 'Updated street')
                ->click("[data-testid=update-{$framework}]")
                ->assertScript(<<<JS
                (() => {
                    const row = document.querySelector('[data-fieldset-row={$framework}]')
                    const fieldset = row.querySelector('fieldset')
                    return fieldset.querySelector('legend').textContent.trim() === 'Updated address*'
                        && fieldset.querySelector('input') === row.originalInput
                        && row.originalInput.value === 'Updated street'
                })()
                JS, true);
        }

        $page->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
