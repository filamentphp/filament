<?php

use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\actingAs;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    FilamentColor::register(['brand' => Color::Violet]);
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-badge-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/badge');
    }
    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('renders `Badge` like Blade and handles native activation, reactive loading, deletion, tooltips and shortcut cleanup', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/badge-primitive-browser-test');
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }
        $page->assertScript("['blade', 'react', 'vue', 'svelte'].every(framework => document.querySelector('[data-badge-row=' + framework + ']').querySelectorAll(':scope > div > :first-child').length === 6)", true);
        $page->assertScript(<<<'JS'
        (() => {
            const tree = element => ({ tag: element.tagName, text: element.children.length ? '' : element.textContent.trim(), children: [...element.children].map(tree), layout: ['color', 'backgroundColor', 'padding', 'fontSize', 'borderRadius'].map(name => getComputedStyle(element)[name]) });
            const trees = framework => [...document.querySelectorAll(`[data-badge-row=${framework}] > div > :first-child`)].map(tree);
            return JSON.stringify(['react', 'vue', 'svelte'].flatMap(framework => trees(framework).map((tree, index) => JSON.stringify(tree) === JSON.stringify(trees('blade')[index]) ? null : {framework, index, actual: tree, expected: trees('blade')[index]}).filter(Boolean)));
        })()
        JS, '[]')->assertNoAccessibilityIssues();
        foreach (['react', 'vue', 'svelte'] as $framework) {
            $host = "document.querySelector('[data-badge-row={$framework}]')";
            $container = "{$host}.children[0]";
            $page->script("{$host}.children[3].element.click(); {$host}.children[4].element.click(); {$host}.children[5].element.querySelector('button').click()");
            $page->assertScript("JSON.stringify([location.hash, document.querySelector('#badge-form').dataset.submits, {$host}.children[5].dataset.deletes, {$host}.children[5].dataset.clicks ?? null])", '["#badge-destination","1","1",null]');
            $page->script("document.querySelector('#badge-form').dataset.submits = '0'; {$host}.update({tag: 'button', tooltip: '<b>Locked</b>', disabled: true, keyBindings: ['alt+b']})");
            $page->assertScript("{$container}.element?.getAttribute('aria-disabled') === 'true' && Boolean({$container}.element?._tippy)", true);
            $page->script("{$container}.original = {$container}.element; {$container}.label = {$container}.element.firstElementChild; {$container}.tooltip = {$container}.element._tippy; {$container}.element.focus(); {$container}.element.click()");
            $page->assertScript("document.activeElement === {$container}.element && !{$container}.dataset.clicks && {$container}.tooltip.props.allowHTML === false && {$container}.tooltip.props.content === '<b>Locked</b>'", true);
            $page->assertScript("{$container}.tooltip.state.isVisible", true);
            $page->assertNoAccessibilityIssues();
            $page->script("window.dispatchEvent(new KeyboardEvent('keydown', {key: 'Escape', bubbles: true}))");
            $page->assertScript("{$container}.tooltip.state.isVisible", false);
            $page->script("{$host}.update({tag: 'button', keyBindings: ['alt+b']})");
            $page->assertScript("{$container}.element === {$container}.original && {$container}.element.firstElementChild === {$container}.label && {$container}.tooltip.state.isDestroyed && !{$container}.element.hasAttribute('aria-disabled') && !{$container}.element.hasAttribute('tabindex')", true);
            $page->click('[data-testid="shortcut-input"]')->keys('[data-testid="shortcut-input"]', 'Alt+b');
            $page->assertScript("{$container}.dataset.clicks", '1');
            $page->script("{$host}.update({tag: 'button', keyBindings: ['alt+b']}, 1)");
            $page->assertScript("{$host}.children[1].element.tagName", 'BUTTON');
            $page->keys('[data-testid="shortcut-input"]', 'Alt+b');
            $page->assertScript("{$host}.children[1].dataset.clicks === '1' && {$container}.dataset.clicks === '1'", true);
            $page->script("{$host}.update({}, 1)");
            $page->assertScript("{$host}.children[1].element.tagName", 'SPAN');
            $page->keys('[data-testid="shortcut-input"]', 'Alt+b');
            $page->assertScript("{$container}.dataset.clicks", '2');
            $page->script("{$host}.update({tag: 'button', keyBindings: ['g p'], tooltip: 'Sequence shortcut'})");
            $page->assertScript("{$container}.element._tippy?.props.content", 'Sequence shortcut');
            $page->keys('[data-testid="shortcut-input"]', 'Alt+b');
            $page->assertScript("{$container}.dataset.clicks", '2');
            $page->keys('[data-testid="shortcut-input"]', ['g', 'p']);
            $page->assertScript("{$container}.dataset.clicks", '3');
            $page->script("{$host}.update({tag: 'button', loading: true, keyBindings: ['alt+b']})");
            $page->assertScript("{$container}.element.disabled && {$container}.element.getAttribute('aria-busy') === 'true' && {$container}.element.contains({$container}.label)", true);
            $page->keys('[data-testid="shortcut-input"]', 'Alt+b');
            $page->assertScript("{$container}.dataset.clicks", '3');
            $page->script("{$host}.update({deletable: true, deleteLoading: true}, 5)");
            $page->assertScript("{$host}.children[5].element.querySelector('button').disabled", true);
            $page->script("{$host}.update({}, 5)");
            $page->assertScript("{$host}.children[5].element.querySelector('button') === null", true);
            $page->script("{$host}.update({tag: 'button', tooltip: 'Available', keyBindings: ['alt+b']})");
            $page->assertScript("Boolean({$container}.element._tippy)", true);
            $page->script("{$container}.tooltip = {$container}.element._tippy; {$host}.destroy()");
            $page->assertScript("{$host}.textContent.trim() === '' && !{$container}.element && {$container}.tooltip.state.isDestroyed", true);
            $page->keys('[data-testid="shortcut-input"]', 'Alt+b');
            $page->assertScript("{$container}.dataset.clicks", '3');
        }
        $page->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
