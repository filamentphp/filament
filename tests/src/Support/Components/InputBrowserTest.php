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
            $page->click("[data-input-row={$framework}] [data-testid=empty]");
            $page->assertScript("{$input}.value === '' && !{$input}.checkValidity()", true)
                ->assertScript("{$host}.dataset.value", $framework === 'svelte' ? 'undefined' : '""');
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
                ->fill("[data-input-row={$framework}] [name=empty]", 'Temporary');
            $page->script("{$defaults}.reset()");
            $page->assertScript("{$defaults}.elements.zero.value === '0' && {$defaults}.elements.empty.value === ''", true);
            $page->click("[data-testid=enabled-{$framework}]")
                ->assertScript("{$defaults}.elements.zero.value === '0' && {$defaults}.elements.empty.value === ''", true);
        }
        $page->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
