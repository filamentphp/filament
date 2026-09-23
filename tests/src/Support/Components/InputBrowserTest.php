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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-input-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/inputs');
    }
    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('preserves `Input` Blade hooks, native values, form semantics and identity across updates', function (): void {
    $page = visit('/input-browser-test');
    foreach (['light', 'dark'] as $theme) {
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }
        $page->assertScript(<<<'JS'
        (() => {
            const blade = document.querySelector('[data-input-row=blade] input');
            const attributes = element => JSON.stringify([...element.attributes].filter(({ name }) => name !== 'value').map(({ name, value }) => [name, name === 'required' ? '' : value]).sort(([first], [second]) => first.localeCompare(second)));
            return ['react', 'vue', 'svelte'].every(framework => {
                const input = document.querySelector(`[data-input-row=${framework}] input`);
                return input && attributes(input) === attributes(blade) && input.value === '0';
            });
        })()
        JS, true)->assertNoAccessibilityIssues();
        foreach (['react', 'vue', 'svelte'] as $framework) {
            $host = "document.querySelector('[data-input-row={$framework}]')";
            $input = "{$host}.querySelector('input')";
            $page->script("window.originalInput = {$input}");
            $page->fill("[data-input-row={$framework}] [name=amount]", '17')
                ->assertScript("{$host}.dataset.value", $framework === 'svelte' ? '17' : '"17"')
                ->assertScript("{$host}.dataset.inputEvent", '17')
                ->assertScript("{$host}.input === window.originalInput", true)
                ->click("[data-testid=disabled-{$framework}]")
                ->assertScript("{$input} === window.originalInput && {$input}.disabled && {$input}.value === '17' && {$input}.classList.contains('fi-input-has-inline-prefix')", true)
                ->assertScript("new FormData({$input}.form).has('amount')", false)
                ->assertNoAccessibilityIssues()
                ->click("[data-testid=readonly-{$framework}]")
                ->assertScript("{$input}.readOnly && !{$input}.disabled && new FormData({$input}.form).get('amount') === '17'", true)
                ->click("[data-testid=enabled-{$framework}]");
            if ($framework !== 'react') {
                $page->assertScript("{$host}.dataset.callbackValue", $framework === 'svelte' ? '17' : '"17"');
            }
            $page->fill("[data-input-row={$framework}] [name=amount]", '');
            $page->assertScript("{$input}.value === '' && !{$input}.checkValidity()", true)
                ->assertScript("{$host}.dataset.value", $framework === 'svelte' ? 'null' : '""');
            if ($framework !== 'react') {
                $page->assertScript("{$host}.dataset.callbackValue", $framework === 'svelte' ? 'null' : '""');
            }
            $page->click("[data-input-row={$framework}] [data-testid=submit]");
            $page->assertScript("{$host}.dataset.submission === undefined", true);
            $page->click("[data-input-row={$framework}] [data-testid=zero]");
            $page->assertScript("{$input}.value === '0' && {$input}.valueAsNumber === 0 && {$input}.checkValidity()", true)
                ->fill("[data-input-row={$framework}] [name=email]", 'invalid')
                ->assertScript("{$input}.form.checkValidity()", false)
                ->fill("[data-input-row={$framework}] [name=email]", 'grace@example.com')
                ->fill("[data-input-row={$framework}] [name=title]", 'Changed');
            $page->click("[data-input-row={$framework}] [data-testid=submit]");
            $page->assertScript("JSON.parse({$host}.dataset.submission)", ['amount' => '0', 'title' => 'Changed', 'email' => 'grace@example.com'])
                ->fill("[data-input-row={$framework}] [name=amount]", '42');
            $page->click("[data-input-row={$framework}] [data-testid=reset]");
            $page->script("delete {$host}.dataset.submission");
            $page->assertScript("{$input} === window.originalInput && {$input}.value === '0' && {$input}.form.elements.title.value === 'Workshop' && {$input}.form.elements.email.value === 'ada@example.com'", true)
                ->assertScript("{$host}.dataset.value", '0');
            $defaults = "{$host}.querySelector('[data-testid=uncontrolled]')";
            $page->assertScript("{$defaults}.elements.zero.value === '0' && {$defaults}.elements.empty.value === ''", true)
                ->fill("[data-input-row={$framework}] [name=zero]", '23')
                ->fill("[data-input-row={$framework}] [name=empty]", 'Temporary')
                ->fill("[data-input-row={$framework}] [name=emptyNumber]", '19');
            $page->script("{$defaults}.addEventListener('reset', event => event.preventDefault(), { once: true }); {$defaults}.reset()");
            $page->assertScript("{$defaults}.elements.zero.value === '23' && {$defaults}.elements.empty.value === 'Temporary' && {$defaults}.elements.emptyNumber.value === '19'", true);
            if ($framework === 'svelte') {
                $page->assertScript("{$host}.dataset.emptyNumber", '19');
            }
            $page->script("{$defaults}.reset()");
            $page->assertScript("{$defaults}.elements.zero.value === '0' && {$defaults}.elements.empty.value === '' && {$defaults}.elements.emptyNumber.value === '' && !{$defaults}.elements.emptyNumber.checkValidity()", true);
            if ($framework === 'svelte') {
                $page->assertScript("{$host}.dataset.emptyNumber", 'null');
            }
            $page->click("[data-testid=enabled-{$framework}]")
                ->assertScript("{$defaults}.elements.zero.value === '0' && {$defaults}.elements.empty.value === ''", true);

            foreach (['text' => '27', 'number' => '31'] as $type => $value) {
                $page->click("[data-testid={$type}-{$framework}]")
                    ->assertScript("{$input} === window.originalInput && {$input}.type === '{$type}'", true)
                    ->fill("[data-input-row={$framework}] [name=amount]", $value)
                    ->assertScript("{$host}.dataset.value", ($framework === 'svelte' && $type === 'number') ? $value : '"' . $value . '"');
                if ($framework !== 'react') {
                    $page->assertScript("{$host}.dataset.callbackValue", ($framework === 'svelte' && $type === 'number') ? $value : '"' . $value . '"');
                }
            }
            $page->click("[data-testid=text-{$framework}]")
                ->fill("[data-input-row={$framework}] [name=amount]", 'Drawing')
                ->assertScript("{$input} === window.originalInput && {$input}.type === 'text'", true)
                ->assertScript("{$host}.dataset.value", '"Drawing"');
            if ($framework === 'vue') {
                $page->click('[data-testid=number-modifier]')
                    ->fill('[data-input-row=vue] [name=amount]', '12.5')
                    ->assertScript("{$host}.dataset.value", '12.5')
                    ->assertScript("{$host}.dataset.callbackValue", '12.5')
                    ->fill('[data-input-row=vue] [name=amount]', '')
                    ->assertScript("{$host}.dataset.value", '""')
                    ->fill('[data-input-row=vue] [name=amount]', 'Drawing')
                    ->assertScript("{$host}.dataset.value", '"Drawing"')
                    ->click('[data-testid=trim-modifier]')
                    ->fill('[data-input-row=vue] [name=amount]', '  Botanical drawing  ')
                    ->assertScript("{$host}.dataset.value", '"Botanical drawing"')
                    ->assertScript("{$host}.dataset.callbackValue", '"Botanical drawing"')
                    ->assertScript("{$input}.hasAttribute('modelmodifiers')", false);
                $page->script("{$input}.dispatchEvent(new CompositionEvent('compositionstart', { bubbles: true })); {$input}.value = '草'; {$input}.dispatchEvent(new InputEvent('input', { bubbles: true, isComposing: true }))");
                $page->assertScript("{$host}.dataset.value", '"草"');
                $page->script("{$input}.dispatchEvent(new CompositionEvent('compositionend', { bubbles: true }))");
            }
            $page->click("[data-testid=enabled-{$framework}]")
                ->click("[data-input-row={$framework}] [data-testid=reset]");
        }
        $page->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
