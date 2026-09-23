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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-actions-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/actions');
    }
    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('renders `Actions` like Blade and preserves focus, child state and native semantics during layout updates in both themes', function (): void {
    $page = visit('/actions-primitive-browser-test');
    foreach (['light', 'dark'] as $theme) {
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }
        $page->assertScript(<<<'JS'
        (() => {
            const tree = element => ({
                tag: element.tagName,
                attributes: [...element.attributes].map(({name, value}) => [name, name === 'class' ? value.split(/\s+/).sort().join(' ') : value]).sort(([first], [second]) => first.localeCompare(second)),
                children: [...element.children].map(tree),
                layout: ['display', 'flexDirection', 'justifyContent', 'gridTemplateColumns', 'gap'].map(property => getComputedStyle(element)[property]),
            });
            const trees = framework => [...document.querySelectorAll(`[data-actions-row=${framework}] > form > div`)].map(tree);
            return trees('blade').length === 11 && ['react', 'vue', 'svelte'].every(framework => JSON.stringify(trees(framework)) === JSON.stringify(trees('blade')));
        })()
        JS, true)->assertNoAccessibilityIssues();
        foreach (['react', 'vue', 'svelte'] as $framework) {
            $selector = "[data-actions-row={$framework}] > form:first-child";
            $host = "document.querySelector('[data-actions-row={$framework}]')";
            $form = "{$host}.firstElementChild";
            $page->fill("{$selector} input", 'Community garden');
            $page->script("{$form}.originalInput = {$form}.querySelector('input'); {$form}.originalRoot = {$form}.firstElementChild; {$form}.originalInput.focus(); {$host}.update({alignment: 'end', fullWidth: true, title: 'Updated'})");
            $page->assertScript("{$form}.element === {$form}.firstElementChild && {$form}.element.title === 'Updated' && getComputedStyle({$form}.element).display === 'grid'", true)
                ->assertScript("{$form}.querySelector('input') === {$form}.originalInput && document.activeElement === {$form}.originalInput && {$form}.originalInput.value === 'Community garden' && {$form}.firstElementChild === {$form}.originalRoot", true)
                ->assertScript("{$form}.dataset.target", 'DIV')
                ->click("{$selector} button[type=submit]")
                ->assertScript("{$form}.dataset.submitted", 'Community garden')
                ->assertScript("{$form}.dataset.actions", '1')
                ->assertScript("{$form}.querySelector('button[disabled]').disabled", true)
                ->click("{$selector} a")
                ->assertScript('location.hash', '#projects')
                ->assertNoAccessibilityIssues();
            $page->script("{$host}.update({alignment: 'start', fullWidth: false, title: undefined})");
            $page->assertScript("!{$form}.element.hasAttribute('title') && getComputedStyle({$form}.element).display === 'flex'", true);
        }
        $page->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
