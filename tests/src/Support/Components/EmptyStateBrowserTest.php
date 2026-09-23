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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-empty-state-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/empty-state');
    }
    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('renders `EmptyState` like Blade and preserves actions and child state during reactive updates in both themes', function (): void {
    $page = visit('/empty-state-browser-test');
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
                text: [...element.childNodes].filter(node => node.nodeType === Node.TEXT_NODE).map(node => node.textContent.trim()).join(''),
            });
            const trees = framework => [...document.querySelectorAll(`[data-empty-state-row=${framework}] > div > div`)].map(tree);
            return ['react', 'vue', 'svelte'].every(framework => JSON.stringify(trees(framework)) === JSON.stringify(trees('blade')));
        })()
        JS, true)->assertNoAccessibilityIssues();

        foreach (['react', 'vue', 'svelte'] as $framework) {
            $selector = "[data-empty-state-row={$framework}] > div:first-child";
            $host = "document.querySelector('{$selector}')";
            $page->script("{$host}.originalInput = {$host}.querySelector('input'); {$host}.originalRoot = {$host}.firstElementChild");
            $page->fill("{$selector} input", 'Community garden')
                ->assertScript("{$host}.dataset.input", 'Community garden')
                ->assertScript("{$host}.dataset.target", 'DIV')
                ->click("{$selector} button")
                ->assertScript("{$host}.dataset.actions", '1')
                ->click("[data-testid=update-{$framework}]")
                ->assertScript("{$host}.querySelector('h3').textContent", 'Updated projects')
                ->assertScript("{$host}.querySelector('input') === {$host}.originalInput && {$host}.originalInput.value === 'Community garden' && {$host}.firstElementChild === {$host}.originalRoot", true)
                ->assertScript("{$host}.firstElementChild.title", 'Updated')
                ->assertScript("{$host}.querySelector('svg') !== null", true)
                ->click("{$selector} button")
                ->assertScript("{$host}.dataset.actions", '2')
                ->click("{$selector} a")
                ->assertScript('location.hash', '#projects')
                ->assertNoAccessibilityIssues()
                ->click("[data-testid=remove-{$framework}]")
                ->assertScript("{$host}.querySelector('footer, p, svg') === null && !{$host}.firstElementChild.hasAttribute('title')", true)
                ->assertScript("{$host}.querySelector('h2').textContent", 'No projects')
                ->click("[data-testid=reset-{$framework}]")
                ->assertScript("{$host}.querySelector('input').value", '')
                ->assertScript("{$host}.element === {$host}.firstElementChild", true);
        }
        $page->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
