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
    foreach (File::allFiles(dirname(__DIR__, 4) . '/build/js-loading-section-renderers') as $file) {
        $id = substr(str_replace('\\', '/', $file->getRelativePathname()), 0, -3);
        FilamentAsset::register([Js::make($id, $file->getPathname())->loadedOnRequest()], 'tests/loading-section');
    }
    Artisan::call('filament:assets');
    actingAs(User::factory()->create());
});

it('renders `LoadingSection` like Blade with reactive layout, native refs and host-owned loading cycles in both themes', function (): void {
    foreach (['light', 'dark'] as $theme) {
        $page = visit('/loading-section-browser-test', ['reducedMotion' => $theme === 'dark' ? 'reduce' : 'no-preference']);
        if ($theme === 'dark') {
            $page = $page->inDarkMode();
        }
        $page->assertScript(<<<'JS'
        (() => {
            const tree = element => ({
                tag: element.tagName,
                attributes: [...element.attributes].filter(({name}) => name !== 'style').map(({name, value}) => [name, name === 'class' ? value.split(/\s+/).filter(Boolean).sort().join(' ') : value]).sort(([first], [second]) => first.localeCompare(second)),
                style: [...element.style].sort().map(name => [name, element.style.getPropertyValue(name).trim()]),
                text: element.textContent.trim(),
                children: [...element.children].map(tree),
                layout: ['height', 'gridColumn', 'gridColumnStart', 'backgroundColor', 'borderRadius'].map(name => getComputedStyle(element)[name]),
            });
            const trees = framework => [...document.querySelectorAll(`[data-loading-section-row=${framework}] > div > div`)].map(tree);
            return trees('blade').length === 9 && ['react', 'vue', 'svelte'].every(framework => JSON.stringify(trees(framework)) === JSON.stringify(trees('blade')));
        })()
        JS, true)->assertNoAccessibilityIssues();
        $page->assertScript("[...document.querySelectorAll('[data-loading-section-row] > div > div')].every(element => getComputedStyle(element).animationName === '" . ($theme === 'dark' ? 'none' : 'pulse') . "')", true);
        foreach ([800, 1200] as $width) {
            $page->resize($width, 900);
            $page->assertScript(<<<'JS'
            [...document.querySelectorAll('[data-loading-section-row]')].every(host => {
                const grid = host.children[3];
                const rectangle = grid.firstElementChild.getBoundingClientRect();
                const parent = grid.getBoundingClientRect();
                const wide = matchMedia('(min-width: 1024px)').matches;
                return Math.abs(rectangle.width - parent.width * (wide ? 0.5 : 0.25)) < 1 && Math.abs(rectangle.left - parent.left - parent.width * (wide ? 0.5 : 0)) < 1 && getComputedStyle(host.children[6].firstElementChild).display === 'none';
            })
            JS, true);
        }
        foreach ([320, 480] as $width) {
            $page->script("document.querySelectorAll('[data-loading-section-row]').forEach(host => host.style.width = '{$width}px')");
            $page->assertScript(<<<'JS'
            [...document.querySelectorAll('[data-loading-section-row]')].every(host => {
                const grid = host.children[7];
                const rectangle = grid.firstElementChild.getBoundingClientRect();
                const parent = grid.getBoundingClientRect();
                const wide = parent.width >= 384;
                return Math.abs(rectangle.width - parent.width * (wide ? 0.5 : 0.25)) < 1 && Math.abs(rectangle.left - parent.left - parent.width * (wide ? 0.5 : 0)) < 1;
            })
            JS, true);
        }
        $page->script("document.querySelectorAll('[data-loading-section-row]').forEach(host => host.style.removeProperty('width'))");
        foreach (['react', 'vue', 'svelte'] as $framework) {
            $host = "document.querySelector('[data-loading-section-row={$framework}]')";
            $container = "{$host}.firstElementChild";
            $page->script("{$container}.original = {$container}.firstElementChild; {$container}.original.dispatchEvent(new MouseEvent('click', {bubbles: true})); {$host}.update({height: '11rem', loadingLabel: 'Loading reports', columnSpan: {default: 'full'}, title: 'Reports'})");
            $page->assertScript("{$container}.element === {$container}.original && {$container}.element.title === 'Reports' && {$container}.element.style.height === '11rem' && {$container}.element.textContent.trim() === 'Loading reports'", true)
                ->assertScript("{$container}.dataset.target", 'DIV');
            $page->script("{$host}.update({height: '11rem', columnSpan: 2, columnStart: 3, style: {height: '9rem', '--col-span-lg': 'span 3 / span 3', '--col-start-lg': '1'}, role: 'region', 'aria-busy': false, 'aria-label': 'Project preview'})");
            $page->assertScript("{$container}.element.style.height === '9rem' && Math.abs({$container}.element.getBoundingClientRect().width - {$container}.getBoundingClientRect().width * 0.75) < 1 && {$container}.element.getBoundingClientRect().left === {$container}.getBoundingClientRect().left", true)
                ->assertScript("{$container}.element.getAttribute('role') === 'region' && {$container}.element.getAttribute('aria-busy') === 'false' && {$container}.element.getAttribute('aria-label') === 'Project preview'", true);
            $page->script("{$host}.update({height: '11rem', columnSpan: 2, columnStart: 3})");
            $page->assertScript("{$container}.element.style.height === '11rem' && {$container}.element.style.getPropertyValue('--col-span-lg').trim() === 'span 2 / span 2' && {$container}.element.style.getPropertyValue('--col-start-lg').trim() === '3' && {$container}.element.getAttribute('role') === 'status' && {$container}.element.getAttribute('aria-busy') === 'true' && !{$container}.element.hasAttribute('aria-label')", true);
            $page->script("{$host}.update({})");
            $page->assertScript("{$container}.element.style.height === '8rem' && !{$container}.element.hasAttribute('title') && !{$container}.element.style.getPropertyValue('--col-span-default')", true);
            foreach (['ready', 'error'] as $state) {
                $page->script("{$host}.update({state: '{$state}'})");
                $page->assertScript("{$container}.querySelector('[aria-busy]') === null && {$container}.textContent.trim() === '{$state}' && !{$container}.element", true);
                $page->script("{$host}.update({})");
                $page->assertScript("{$container}.element?.getAttribute('aria-busy')", 'true');
            }
            $page->script("{$host}.destroy()");
            $page->assertScript("{$host}.textContent.trim() === '' && !{$container}.element", true);
        }
        $page->assertNoSmoke()->assertNoAccessibilityIssues();
    }
});
