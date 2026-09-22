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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-radio-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/radios');
    }

    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('preserves native `Radio` groups, form behavior and reactive bindings across renderers', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/radio-primitive-browser-test');
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }

        $page->assertScript("document.querySelectorAll('[data-radio-row] input').length", 28)
            ->assertScript("document.querySelector('[data-radio-row=react]').dataset.nativeRef", 'true')
            ->assertScript(<<<'JS'
            (() => {
                const inputs = (framework) => [...document.querySelectorAll(`[data-radio-row=${framework}] input`)].map(element => ({
                    type: element.type, name: element.name, value: element.value, checked: element.checked,
                    disabled: element.disabled, required: element.required, classes: [...element.classList].sort(),
                    state: element.dataset.state, autocomplete: element.autocomplete,
                    associated: element.form?.id === `radio-form-${framework}`,
                }))
                return ['react', 'vue', 'svelte'].every(framework => JSON.stringify(inputs(framework)) === JSON.stringify(inputs('blade')))
            })()
            JS, true)
            ->assertNoAccessibilityIssues();

        $page->script("window.radioElements = [...document.querySelectorAll('[data-radio-row] input')]");

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $selector = "[data-radio-row={$framework}]";
            $form = "document.getElementById('radio-form-{$framework}')";

            $page->assertScript("JSON.stringify([...new FormData({$form})])", '[["delivery","standard"],["separate","saved"],["controlled","standard"]]')
                ->click("[data-testid=reset-{$framework}]")
                ->assertScript("JSON.stringify([...new FormData({$form})])", '[["delivery","standard"],["separate","saved"],["controlled","standard"]]')
                ->assertScript("{$form}.checkValidity()", false)
                ->click("{$selector} input[name=delivery][value=express]")
                ->assertScript("document.querySelector('{$selector} input[name=delivery][value=standard]').checked", false)
                ->click("{$selector} input[name=required]")
                ->assertScript("{$form}.checkValidity()", true)
                ->click("[data-testid=invalidate-{$framework}]")
                ->assertScript("[...document.querySelectorAll('{$selector} input')].every(element => element.classList.contains('fi-invalid') && !element.classList.contains('fi-valid'))", true)
                ->assertScript("document.querySelector('{$selector} input[name=delivery]').classList.contains('custom-radio')", true)
                ->assertScript("JSON.stringify([...new FormData({$form})])", '[["delivery","express"],["separate","saved"],["required","on"],["controlled","standard"]]')
                ->click("[data-testid=select-{$framework}]")
                ->assertScript("document.querySelector('{$selector} output').dataset.model", 'express')
                ->assertScript("document.querySelector('{$selector} input[name=controlled][value=express]').checked", true)
                ->click("{$selector} input[name=controlled][value=standard]")
                ->assertScript("document.querySelector('{$selector} output').dataset.model", 'standard')
                ->assertScript("document.querySelector('{$selector} input[name=controlled][value=standard]').dataset.modelAtChange", 'standard')
                ->assertScript("document.querySelector('{$selector}').dataset.changes", '3');

            $page->keys("{$selector} input[name=controlled][value=standard]", 'ArrowRight')
                ->assertScript("document.querySelector('{$selector} output').dataset.model", 'express')
                ->assertScript("document.querySelector('{$selector} input[name=controlled][value=express]').dataset.modelAtChange", 'express')
                ->assertScript("document.querySelector('{$selector} input[name=controlled][value=standard]').checked", false)
                ->assertScript("document.querySelector('{$selector}').dataset.changes", '4')
                ->assertScript("new FormData({$form}).get('controlled')", 'express')
                ->click("[data-testid=reset-{$framework}]")
                ->assertScript("JSON.stringify([...new FormData({$form})])", '[["delivery","standard"],["separate","saved"],["controlled","standard"]]')
                ->assertScript("document.querySelector('{$selector} output').dataset.model", 'standard')
                ->click("[data-testid=reset-{$framework}]")
                ->assertScript("JSON.stringify([...new FormData({$form})])", '[["delivery","standard"],["separate","saved"],["controlled","standard"]]')
                ->assertScript("document.querySelector('{$selector} input[name=disabled]').checked", true)
                ->assertScript("{$form}.checkValidity()", false);
        }

        $page->assertScript("[...document.querySelectorAll('[data-radio-row] input')].every((element, index) => element === window.radioElements[index])", true)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});
