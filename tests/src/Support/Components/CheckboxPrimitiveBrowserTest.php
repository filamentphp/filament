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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-checkbox-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/checkboxes');
    }

    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('preserves native `Checkbox` state, form behavior and reactive bindings across renderers', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/checkbox-primitive-browser-test');
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }

        $page->assertScript("document.querySelectorAll('[data-checkbox-row] input').length", 24)
            ->assertScript("document.querySelector('[data-checkbox-row=react]').dataset.nativeRef", 'true')
            ->assertScript(<<<'JS'
            (() => {
                const inputs = (framework) => [...document.querySelectorAll(`[data-checkbox-row=${framework}] input`)].map(element => ({
                    type: element.type, name: element.name, value: element.value, checked: element.checked,
                    disabled: element.disabled, required: element.required, indeterminate: element.indeterminate,
                    classes: [...element.classList].sort(), state: element.dataset.state, autocomplete: element.autocomplete,
                    associated: element.form?.id === `checkbox-form-${framework}`,
                }))
                return ['react', 'vue', 'svelte'].every(framework => JSON.stringify(inputs(framework)) === JSON.stringify(inputs('blade')))
            })()
            JS, true)
            ->assertNoAccessibilityIssues();

        $page->script("window.checkboxElements = [...document.querySelectorAll('[data-checkbox-row] input')]");

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $selector = "[data-checkbox-row={$framework}]";
            $form = "document.getElementById('checkbox-form-{$framework}')";

            $page->assertScript("JSON.stringify([...new FormData({$form})])", '[["initial","saved"]]')
                ->assertScript("{$form}.checkValidity()", false)
                ->click("{$selector} input[name=unchecked]")
                ->click("{$selector} input[name=initial]")
                ->click("{$selector} input[name=required]")
                ->assertScript("{$form}.checkValidity()", true)
                ->assertScript("JSON.stringify([...new FormData({$form})])", '[["unchecked","yes"],["required","on"]]')
                ->click("[data-testid=invalidate-{$framework}]")
                ->assertScript("[...document.querySelectorAll('{$selector} input')].every(element => element.classList.contains('fi-invalid') && !element.classList.contains('fi-valid'))", true)
                ->assertScript("document.querySelector('{$selector} input[name=unchecked]').classList.contains('custom-checkbox')", true)
                ->assertScript("JSON.stringify([...new FormData({$form})])", '[["unchecked","yes"],["required","on"]]')
                ->click("[data-testid=toggle-{$framework}]")
                ->assertScript("document.querySelector('{$selector} input[name=controlled]').checked", true)
                ->assertScript("document.querySelector('{$selector} output').dataset.model", 'true')
                ->assertScript("new FormData({$form}).get('controlled')", 'enabled')
                ->click("{$selector} input[name=controlled]")
                ->assertScript("document.querySelector('{$selector} output').dataset.model", 'false')
                ->assertScript("new FormData({$form}).has('controlled')", false)
                ->click("[data-testid=toggle-{$framework}]")
                ->assertScript("document.querySelector('{$selector} input[name=controlled]').checked", true)
                ->click("[data-testid=toggle-{$framework}]")
                ->assertScript("document.querySelector('{$selector} input[name=controlled]').checked", false)
                ->assertScript("document.querySelector('{$selector}').dataset.changes", '4')
                ->click("{$selector} input[name=mixed]")
                ->assertScript("document.querySelector('{$selector} input[name=mixed]').indeterminate", false)
                ->assertScript("new FormData({$form}).get('mixed')", 'on')
                ->click("[data-testid=toggle-{$framework}]")
                ->assertScript("document.querySelector('{$selector} input[name=controlled]').checked", true)
                ->click("[data-testid=toggle-{$framework}]")
                ->assertScript("document.querySelector('{$selector} input[name=controlled]').checked", false)
                ->assertScript("document.querySelector('{$selector} input[name=mixed]').indeterminate", false)
                ->click("[data-testid=reset-{$framework}]")
                ->assertScript("JSON.stringify([...new FormData({$form})])", '[["initial","saved"]]')
                ->assertScript("document.querySelector('{$selector} input[name=disabled]').checked", true)
                ->assertScript("{$form}.checkValidity()", false);

            if ($framework === 'svelte') {
                $page->click("[data-testid=toggle-{$framework}]")
                    ->assertScript("document.querySelector('{$selector} output').dataset.model", 'true')
                    ->click("[data-testid=reset-{$framework}]")
                    ->assertScript("document.querySelector('{$selector} output').dataset.model", 'false')
                    ->assertScript("document.querySelector('{$selector} input[name=controlled]').checked", false);
            }
        }

        $page->script("const row = document.querySelector('[data-checkbox-row=vue]'); row.dataset.initialChanges = row.dataset.changes");
        foreach ([true, false] as $checked) {
            $page->click('[data-checkbox-row=vue] input[name=controlled]')
                ->assertScript("document.querySelector('[data-checkbox-row=vue] input[name=controlled]').dataset.modelAtChange", $checked ? 'true' : 'false')
                ->assertScript("Number(document.querySelector('[data-checkbox-row=vue]').dataset.changes) - Number(document.querySelector('[data-checkbox-row=vue]').dataset.initialChanges)", $checked ? 1 : 2);
        }

        $page->assertScript("[...document.querySelectorAll('[data-checkbox-row] input')].every((element, index) => element === window.checkboxElements[index])", true)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});
