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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-select-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/selects');
    }
    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('preserves `Select` native selection, defaults, validation and identity in both themes', function (): void {
    $page = visit('/select-browser-test');
    foreach (['light', 'dark'] as $theme) {
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }
        $page->assertScript(<<<'JS'
        (() => {
            const blade = document.querySelector('[data-select-row=blade] select');
            const attributes = element => JSON.stringify([...element.attributes].map(({name, value}) => [name, name === 'required' ? '' : value]).sort(([first], [second]) => first.localeCompare(second)));
            return ['react', 'vue', 'svelte'].every(framework => {
                const select = document.querySelector(`[data-select-row=${framework}] select`);
                return select && attributes(select) === attributes(blade) && select.value === 'drawing';
            });
        })()
        JS, true)->assertNoAccessibilityIssues();
        foreach (['react', 'vue', 'svelte'] as $framework) {
            $selector = "[data-select-row={$framework}]";
            $host = "document.querySelector('{$selector}')";
            $select = "{$host}.querySelector('[name=workshop]')";
            $multiple = "{$host}.querySelector('[name=extras]')";
            $defaults = "{$host}.querySelector('[data-testid=defaults]')";
            $page->script("window.originalSelect = {$select}");
            $page->select("{$selector} [name=workshop]", 'ceramics')
                ->assertScript("{$host}.dataset.value", '"ceramics"')
                ->assertScript("{$host}.select === window.originalSelect", true)
                ->assertScript("{$host}.dataset.callbackValue", $framework === 'react' ? '"drawing"' : '"ceramics"')
                ->select("{$selector} [name=extras]", ['drawing', 'ceramics'])
                ->assertScript("{$host}.dataset.multiple", '["drawing","ceramics"]')
                ->click("{$selector} [data-testid=submit]")
                ->assertScript("JSON.parse({$host}.dataset.submission)", [['workshop', 'ceramics'], ['extras', 'drawing'], ['extras', 'ceramics']])
                ->click("[data-testid=disabled-{$framework}]")
                ->assertScript("{$select} === window.originalSelect && {$select}.disabled && {$select}.value === 'ceramics' && !new FormData({$select}.form).has('workshop')", true)
                ->assertNoAccessibilityIssues()
                ->click("[data-testid=enabled-{$framework}]")
                ->click("{$selector} [data-testid=empty]")
                ->assertScript("{$select}.value === '' && !{$select}.checkValidity() && {$multiple}.selectedOptions.length === 0", true);
            $page->script("delete {$host}.dataset.submission");
            $page->click("{$selector} [data-testid=submit]")
                ->assertScript("{$host}.dataset.submission === undefined", true)
                ->click("{$selector} [data-testid=archived]")
                ->assertScript("{$select}.value === 'archived' && {$select}.checkValidity() && {$multiple}.selectedOptions.length === 2", true)
                ->click("{$selector} [data-testid=submit]")
                ->assertScript("JSON.parse({$host}.dataset.submission)", [['extras', 'drawing']])
                ->click("{$selector} [data-testid=reset]")
                ->assertScript("{$select}.value === 'drawing' && {$host}.dataset.value === '\"drawing\"' && {$host}.dataset.multiple === '[\"ceramics\"]'", true)
                ->select("{$selector} [name=extras]", ['drawing', 'ceramics'])
                ->click("[data-testid=remove-{$framework}]")
                ->assertScript("{$select} === window.originalSelect && {$select}.value === '' && {$host}.dataset.value === '\"drawing\"'", true)
                ->assertScript("JSON.stringify([...{$multiple}.selectedOptions].map(option => option.value)) === '[\"ceramics\"]' && {$host}.dataset.multiple === '[\"drawing\",\"ceramics\"]'", true)
                ->assertScript("{$select}.selectedIndex", $framework === 'react' ? 0 : -1)
                ->click("[data-testid=enabled-{$framework}]")
                ->assertScript("{$select}.value", 'drawing')
                ->assertScript("JSON.stringify([...{$multiple}.selectedOptions].map(option => option.value))", '["drawing","ceramics"]')
                ->assertScript("JSON.stringify([...{$defaults}.elements.defaultExtras.selectedOptions].map(option => option.value))", '["ceramics","archived"]')
                ->select("{$selector} [name=defaultExtras]", ['drawing'])
                ->select("{$selector} [name=defaultWorkshop]", 'drawing');
            $page->script("{$defaults}.addEventListener('reset', event => event.preventDefault(), { once: true }); {$defaults}.reset()");
            $page->assertScript("{$defaults}.elements.defaultWorkshop.value", 'drawing');
            $page->script("{$defaults}.reset()");
            $page->assertScript("{$defaults}.elements.defaultWorkshop.value", 'ceramics')
                ->assertScript("JSON.stringify([...{$defaults}.elements.defaultExtras.selectedOptions].map(option => option.value))", '["ceramics","archived"]')
                ->assertScript("new FormData({$defaults}).getAll('defaultExtras')", ['ceramics'])
                ->click("[data-testid=enabled-{$framework}]")
                ->assertScript("{$defaults}.elements.defaultWorkshop.value", 'ceramics');
        }
        $page->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
