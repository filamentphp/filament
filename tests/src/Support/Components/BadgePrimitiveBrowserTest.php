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
        $page->assertScript(<<<'JS'
        (() => {
            const {interactive, applicationShortcuts} = document.querySelector('[data-badge-row=react]');
            const fixture = document.createElement('div');
            document.body.append(fixture);
            const failures = [];
            const check = (name, actual, expected) => { if (actual !== expected) failures.push({name, actual, expected}); };
            const button = () => {
                const element = document.createElement('button');
                element.textContent = 'Shortcut';
                element.count = 0;
                element.onclick = () => element.count++;
                fixture.append(element);
                return element;
            };
            const press = (key, modifiers = {}) => {
                document.dispatchEvent(new KeyboardEvent('keydown', {key, keyCode: key.toUpperCase().charCodeAt(0), bubbles: true, ...modifiers}));
                document.dispatchEvent(new KeyboardEvent('keypress', {key, keyCode: key.charCodeAt(0), bubbles: true, ...modifiers}));
                document.dispatchEvent(new KeyboardEvent('keyup', {key, keyCode: key.toUpperCase().charCodeAt(0), bubbles: true, ...modifiers}));
            };
            let applicationCount = 0;
            applicationShortcuts.bind('alt+z', () => applicationCount++);
            const first = button(), last = button();
            const removeFirst = interactive(first, {keyBindings: ['alt+b']});
            const removeLast = interactive(last, {keyBindings: ['option+b']});
            press('b', {altKey: true});
            check('alias single winner', `${first.count},${last.count}`, '0,1');
            for (const [name, hide, restore] of [
                ['hidden ancestor', () => { fixture.hidden = true; document.body.append(first); }, () => { fixture.hidden = false; fixture.prepend(first); }],
                ['visibility', () => last.style.visibility = 'hidden', () => last.style.visibility = ''],
                ['inert ancestor', () => { fixture.inert = true; document.body.append(first); }, () => { fixture.inert = false; fixture.prepend(first); }],
                ['native disabled', () => last.disabled = true, () => last.disabled = false],
            ]) {
                const count = first.count;
                hide(); press('b', {altKey: true});
                check(name, first.count, count + 1);
                check(`${name} skips last`, last.count, 1);
                restore();
            }
            const lower = document.createElement('div'), upper = document.createElement('div');
            for (const modal of [lower, upper]) { modal.setAttribute('aria-modal', 'true'); fixture.append(modal); }
            lower.append(last); upper.append(first);
            press('b', {altKey: true});
            check('last visible modal, not last registration', first.count, 5);
            check('underlying modal blocked', last.count, 1);
            const closed = document.createElement('div'); closed.hidden = true; fixture.append(closed); closed.append(upper);
            press('b', {altKey: true});
            check('closed ancestor modal ignored', last.count, 2);
            fixture.prepend(first, last); lower.remove(); closed.remove();
            removeLast(); removeLast();
            press('b', {altKey: true});
            check('alias fallback and repeated cleanup', first.count, 6);
            removeFirst();
            for (const [keys, modifiers] of [
                [['alt+shift+b', 'shift+option+b'], {altKey: true, shiftKey: true}],
                [['mod+b', /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? 'command+b' : 'ctrl+b'], /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? {metaKey: true} : {ctrlKey: true}],
            ]) {
                first.count = last.count = 0;
                const cleanupFirst = interactive(first, {keyBindings: [keys[0]]});
                const cleanupLast = interactive(last, {keyBindings: [keys[1]]});
                press('b', modifiers);
                check('modifier alias winner', `${first.count},${last.count}`, '0,1');
                cleanupLast(); press('b', modifiers);
                check('modifier alias fallback', `${first.count},${last.count}`, '1,1');
                cleanupFirst();
            }
            first.count = last.count = 0;
            const removeSequence = interactive(last, {keyBindings: ['g p']});
            press('g'); removeSequence();
            const removeSingle = interactive(first, {keyBindings: ['g']});
            press('p'); press('g');
            check('sequence prefix removed', `${first.count},${last.count}`, '1,0');
            removeSingle(); removeSingle(); press('g');
            check('all registrations removed', first.count, 1);
            press('z', {altKey: true});
            check('application singleton survives rebuilds', applicationCount, 1);
            applicationShortcuts.unbind('alt+z');
            fixture.remove();
            return JSON.stringify(failures);
        })()
        JS, '[]');
        foreach (['react', 'vue', 'svelte'] as $framework) {
            $host = "document.querySelector('[data-badge-row={$framework}]')";
            $container = "{$host}.children[0]";
            $page->script("{$host}.children[3].element.click(); {$host}.children[4].element.click(); {$host}.children[5].element.querySelector('button').click()");
            $page->assertScript("JSON.stringify([location.hash, document.querySelector('#badge-form').dataset.submits, {$host}.children[5].dataset.deletes, {$host}.children[5].dataset.clicks ?? null])", '["#badge-destination","1","1",null]');
            $page->script("document.querySelector('#badge-form').dataset.submits = '0'; {$host}.update({tag: 'button', tooltip: '<b>Locked</b>', 'data-tippy-allowhtml': 'true', 'data-tippy-content': '<img src=x onerror=alert(1)>', disabled: true, keyBindings: ['alt+b']})");
            $page->assertScript("{$container}.element?.getAttribute('aria-disabled') === 'true' && Boolean({$container}.element?._tippy)", true);
            $page->script("{$container}.original = {$container}.element; {$container}.label = {$container}.element.firstElementChild; {$container}.tooltip = {$container}.element._tippy; {$container}.element.focus(); {$container}.element.click()");
            $page->assertScript("document.activeElement === {$container}.element && !{$container}.dataset.clicks && {$container}.tooltip.props.allowHTML === false && {$container}.tooltip.props.content === '<b>Locked</b>' && !{$container}.tooltip.popper.querySelector('img, b')", true);
            $page->assertScript("{$container}.tooltip.state.isVisible", true);
            $page->assertNoAccessibilityIssues();
            $page->script("window.dispatchEvent(new KeyboardEvent('keydown', {key: 'Escape', bubbles: true}))");
            $page->assertScript("{$container}.tooltip.state.isVisible", false);
            $page->script("{$host}.update({tag: 'a', href: '#blocked-link', disabled: true, tooltip: 'Unavailable'}, 3)");
            $page->assertScript("{$host}.children[3].element.getAttribute('role') === 'link' && !{$host}.children[3].element.hasAttribute('href') && Boolean({$host}.children[3].element._tippy)", true);
            $page->script("{$host}.children[3].element.focus(); {$host}.children[3].element.click()");
            $page->keys('[data-badge-row="' . $framework . '"] > div:nth-child(4) > a', 'Enter');
            $page->assertScript("location.hash === '#badge-destination' && document.activeElement === {$host}.children[3].element && {$host}.children[3].element._tippy.state.isVisible && {$host}.children[3].dataset.clicks === '1'", true);
            $page->assertNoAccessibilityIssues();
            $page->script("{$host}.update({tag: 'a', href: '#badge-destination'}, 3)");
            $page->assertScript("{$host}.children[3].element.getAttribute('href') === '#badge-destination' && !{$host}.children[3].element.hasAttribute('role')", true);
            $page->script("{$host}.update({tag: 'a', href: '#badge-destination', disabled: true, role: 'button'}, 3)");
            $page->assertScript("{$host}.children[3].element.getAttribute('role')", 'button');
            $page->script("{$host}.update({tag: 'a', href: '#badge-destination'}, 3)");
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
