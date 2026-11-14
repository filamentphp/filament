<?php

namespace Filament\Tests\Support;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
});

it('loads independent values, whole columns, rows, and entries then refreshes with the parent', function (): void {
    $page = visit('/lazy-loaded-state-browser-test');

    $page->page()->context()->addInitScript(<<<'JS'
        window.lazyLoadedStateRequests = [];
        window.lazyLoadedStateResults = [];
        window.addEventListener('lazy-loaded-state-resolved', event => window.lazyLoadedStateResults.push(event.detail));
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ options, succeed }) => {
                const request = JSON.parse(options.body);
                succeed(({ status }) => {
                    window.lazyLoadedStateRequests.push({ status, components: request.components.map(component => component.calls) });
                });
            });
        });
        JS);
    $page->refresh();

    $page->assertSee('Independent entry v1')
        ->assertSee('Total: 0')
        ->assertSee('No result')
        ->assertSee('Individual Alpha v1')
        ->assertSee('Individual Beta v1')
        ->assertSee('Column Alpha / 2')
        ->assertSee('Column Beta / 2')
        ->assertSee('First Alpha')
        ->assertSee('Second Alpha')
        ->assertSee('First Beta')
        ->assertSee('Second Beta')
        ->assertDontSee('Private entry')
        ->assertDontSee('Private column')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $requests = $page->script('window.lazyLoadedStateRequests');

    expect($requests)->toHaveCount(7);

    $targetCounts = [];

    foreach ($requests as $request) {
        expect($request['status'])->toBe(200)
            ->and($request['components'])->toHaveCount(1)
            ->and($request['components'][0])->toHaveCount(1);

        $call = $request['components'][0][0];
        $targetCounts[] = count(($call['method'] === 'callSchemaComponentMethod') ? $call['params'][2][0] : $call['params'][0]);
    }

    sort($targetCounts);

    expect($targetCounts)->toBe([1, 1, 1, 2, 2, 2, 2]);

    expect($page->script('window.lazyLoadedStateResults'))->toEqualCanonicalizing([
        ['name' => 'Alpha', 'revision' => 1],
        ['name' => 'Beta', 'revision' => 1],
    ]);

    $page->click('Refresh state')
        ->assertSee('Independent entry v2')
        ->assertSee('Individual Alpha v2')
        ->assertSee('Individual Beta v2')
        ->assertSee('Column Beta / 2')
        ->assertNoSmoke();

    $page->inDarkMode()
        ->assertSee('Independent entry v1')
        ->assertSee('Column Beta / 2')
        ->assertNoAccessibilityIssues();
});

it('waits for intersection while loading all rendered members of an explicit group together', function (): void {
    $page = visit('/lazy-loaded-state-browser-test?viewport=1');

    $page->page()->context()->addInitScript(<<<'JS'
        window.viewportStateCalls = [];
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ options }) => {
                for (const component of JSON.parse(options.body).components) {
                    for (const call of component.calls) {
                        if (call.method === 'callSchemaComponentMethod' && call.params[1] === 'loadState') {
                            window.viewportStateCalls.push(call);
                        }
                    }
                }
            });
        });
        JS);
    $page->refresh()
        ->assertSee('Viewport controls are ready')
        ->click('Toggle values')
        ->assertMissing('[data-testid="viewport-values"]')
        ->click('Toggle values')
        ->assertPresent('[data-testid="viewport-values"] [aria-busy="true"]');

    expect($page->script('window.viewportStateCalls'))->toBe([]);

    $page->page()->locator('[data-testid="viewport-values"] [aria-expanded]')->click();
    $page->assertSee('First independent value')
        ->assertSee('First grouped value / 2')
        ->assertSeeIn('[data-testid="last-grouped-value"]', 'Last grouped value / 2')
        ->assertDontSee('Last independent value');

    expect($page->script('window.viewportStateCalls.map(call => Object.keys(call.params[2][0]).length).sort()'))->toBe([1, 2]);

    $page->page()->locator('[data-testid="last-independent-value"]')->scrollIntoViewIfNeeded();
    $page->assertSee('Last independent value')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    expect($page->script('window.viewportStateCalls'))->toHaveCount(3);

    $darkPage = $page->inDarkMode();
    $darkPage->page()->locator('[data-testid="viewport-values"] [aria-expanded]')->click();
    $darkPage->assertSee('First grouped value / 2');
    $darkPage->page()->locator('[data-testid="last-independent-value"]')->scrollIntoViewIfNeeded();
    $darkPage->assertSee('Last independent value')
        ->assertNoAccessibilityIssues();
});

it('keeps other groups responsive after a failure and discards responses for replaced cells', function (): void {
    $page = visit('/lazy-loaded-state-browser-test');

    $requestScript = <<<'JS'
        window.lazyLoadedStateResults = [];
        window.addEventListener('lazy-loaded-state-resolved', event => window.lazyLoadedStateResults.push(event.detail));
        const originalFetch = window.fetch;
        let hasDelayed = false;
        let hasFailed = false;
        document.addEventListener('livewire:init', () => {
            Livewire.interceptRequest(({ request, onResponse }) => {
                const component = request.payload.components[0];
                const call = component.calls[0];
                if (call?.method !== 'loadTableColumnStates') return;
                const target = Object.values(call.params[0])[0];
                if (target.column !== 'individual' || target.record !== '11' || JSON.parse(component.snapshot).data.revision !== 1) return;
                onResponse(() => requestAnimationFrame(() => window.dispatchEvent(new Event('delayed-lazy-loaded-state-response-processed'))));
            });
        });
        window.fetch = async (...arguments) => {
            const response = await originalFetch(...arguments);
            const request = arguments[1]?.body ? JSON.parse(arguments[1].body) : null;
            const call = request?.components?.[0]?.calls?.[0];
            if (call?.method !== 'loadTableColumnStates') return response;
            const target = Object.values(call.params[0])[0];
            if (target.column !== 'individual') return response;
            if (target.record === '11' && !hasDelayed) {
                hasDelayed = true;
                const content = await response.text();
                await new Promise(resolve => { window.releaseLazyLoadedStateResponse = resolve; });
                return new Response(content, { status: response.status, headers: response.headers });
            } else if (target.record === '37' && !hasFailed) {
                hasFailed = true;
                return new Response('{}', { status: 503 });
            }
            return response;
        };
        JS;
    $page->page()->context()->addInitScript($requestScript);
    $page->refresh();

    $page->assertSee('Independent entry v1')
        ->assertSee('Column Beta / 2')
        ->assertVisible('[role="alert"]:visible')
        ->assertPresent('[aria-busy="true"]')
        ->assertDontSee('Individual Alpha v1')
        ->assertNoAccessibilityIssues();

    $page->click('Refresh state')
        ->assertSee('Individual Alpha v2')
        ->assertSee('Individual Beta v2')
        ->assertSee('Independent entry v2');

    $page->script(<<<'JS'
        async () => {
            const processed = new Promise(resolve => window.addEventListener('delayed-lazy-loaded-state-response-processed', resolve, { once: true }));
            window.releaseLazyLoadedStateResponse();
            await processed;
        }
        JS);

    expect($page->script(<<<'JS'
        () => {
            const root = document.querySelector('[data-testid="lazy-loaded-state-note"]').closest('[wire\\:id]');
            return Livewire.find(root.getAttribute('wire:id')).$get('revision');
        }
        JS))->toBe(2);

    $page->assertDontSee('Individual Alpha v1')
        ->assertSee('Individual Alpha v2')
        ->assertNoSmoke();

    expect($page->script('window.lazyLoadedStateResults.some(event => event.name === "Alpha" && event.revision === 1)'))->toBeFalse();

    $page->click('Refresh state')
        ->assertSee('Individual Alpha v3')
        ->assertSee('Independent entry v3')
        ->assertNoSmoke();

    $darkPage = $page->inDarkMode();
    $darkPage->page()->context()->addInitScript($requestScript);
    $darkPage->refresh()
        ->assertVisible('[role="alert"]:visible')
        ->assertNoAccessibilityIssues();
});

it('loads nested lazy parents and supports their child confirmation actions', function (bool $lazyItemSchemas): void {
    $page = visit('/lazy-loaded-repeatable-entry-browser-test' . ($lazyItemSchemas ? '?lazyItemSchemas=1' : ''));

    $page->assertSee('Parcel 1048 / box v1')
        ->assertSee('Parcel 1056 / box v1')
        ->assertSee('Two deliveries')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $page->click('Select Parcel 1056')
        ->assertVisible('[role="alertdialog"] button[type="submit"]')
        ->assertNoAccessibilityIssues()
        ->click('Confirm')
        ->assertSee('Parcel 1056')
        ->assertDontSee('No parcel selected')
        ->assertSee('Parcel 1048 / box v1')
        ->click('Refresh parcels')
        ->assertSee('Parcel 1048 / box v2')
        ->assertSee('Parcel 1056 / box v2')
        ->assertNoSmoke();

    $darkPage = $page->inDarkMode();
    $darkPage->assertSee('Parcel 1056 / box v1')
        ->assertNoAccessibilityIssues()
        ->click('Select Parcel 1048')
        ->assertVisible('[role="alertdialog"] button[type="submit"]')
        ->assertNoAccessibilityIssues();
})->with(['eager item schemas' => false, 'lazy item schemas' => true]);

it('includes deferred parent edits in native lazy requests without losing them', function (): void {
    $page = visit('/lazy-loaded-state-browser-test');

    $page->page()->context()->addInitScript(<<<'JS'
        window.lazyLoadedStateUpdates = [];
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ options }) => {
                const request = JSON.parse(options.body);
                for (const component of request.components) {
                    window.lazyLoadedStateUpdates.push(component.updates);
                }
            });
        });
        document.addEventListener('livewire:initialized', () => {
            const root = document.querySelector('[data-testid="lazy-loaded-state-note"]').closest('[wire\\:id]');
            Livewire.find(root.getAttribute('wire:id')).$set('note', 'Edited note', false);
        });
        JS);
    $page->refresh();

    $page->assertSee('Individual Alpha v1')
        ->assertSee('Individual Beta v1')
        ->assertSee('Independent entry v1')
        ->assertSee('Column Beta / 2')
        ->assertSee('First Beta')
        ->assertSee('Second Beta')
        ->assertValue('Note', 'Edited note');

    expect($page->script('window.lazyLoadedStateUpdates.slice(0, 7)'))->toBe(array_fill(0, 7, ['note' => 'Edited note']));

    $page->click('Refresh state')
        ->assertSee('Eager entry: Edited note')
        ->assertSee('Individual Alpha v2')
        ->assertValue('Note', 'Edited note')
        ->assertNoSmoke();
});

it('keeps stale independent and grouped values deferred while their section is collapsed', function (): void {
    $page = visit('/lazy-loaded-state-browser-test?viewport=1');
    $page->page()->context()->addInitScript(<<<'JS'
        const originalFetch = window.fetch;
        window.heldLazyStateResponses = [];
        window.staleStateCalls = [];
        window.processedStaleStateResponses = 0;
        window.addEventListener('lazy-loaded-state-advanced', event => {
            window.lazyLoadedStateRevision = event.detail.revision;
        });
        document.addEventListener('livewire:init', () => {
            Livewire.interceptRequest(({ request, onResponse }) => {
                const component = request.payload.components[0];
                const call = component.calls[0];
                if (call?.method !== 'callSchemaComponentMethod' || call.params[1] !== 'loadState') return;
                window.staleStateCalls.push(call);
                if (JSON.parse(component.snapshot).data.revision !== 1) return;
                onResponse(() => requestAnimationFrame(() => requestAnimationFrame(() => {
                    window.processedStaleStateResponses++;
                })));
            });
        });
        window.fetch = async (...arguments) => {
            const response = await originalFetch(...arguments);
            const component = arguments[1]?.body ? JSON.parse(arguments[1].body).components?.[0] : null;
            const call = component?.calls?.[0];
            if (call?.method !== 'callSchemaComponentMethod' || call.params[1] !== 'loadState' || JSON.parse(component.snapshot).data.revision !== 1) return response;
            const content = await response.text();
            await new Promise(resolve => window.heldLazyStateResponses.push(resolve));
            return new Response(content, { status: response.status, headers: response.headers });
        };
        JS);
    $page->refresh()->assertSee('Viewport controls are ready');
    $page->page()->locator('[data-testid="viewport-values"] [aria-expanded]')->click();
    $page->assertScript('window.heldLazyStateResponses.length', 2)
        ->click('Advance state')
        ->assertScript('window.lazyLoadedStateRevision', 2);
    $page->page()->locator('[data-testid="viewport-values"] [aria-expanded]')->click();
    $page->assertAttribute('[data-testid="viewport-values"] [aria-expanded]', 'aria-expanded', 'false');
    $page->script('window.heldLazyStateResponses.forEach(release => release())');
    $page->assertScript('window.processedStaleStateResponses', 2);

    expect($page->script('window.staleStateCalls'))->toHaveCount(2);

    $page->page()->locator('[data-testid="viewport-values"] [aria-expanded]')->click();
    $page->assertSee('First independent value v2')
        ->assertSee('First grouped value / 2 v2')
        ->assertSeeIn('[data-testid="last-grouped-value"]', 'Last grouped value / 2 v2')
        ->assertDontSee('Last independent value')
        ->assertNoSmoke();

    expect($page->script('window.staleStateCalls'))->toHaveCount(4);
});

it('reloads a surviving placeholder after a renderless locked-state update without rolling back the parent', function (): void {
    $page = visit('/lazy-loaded-state-browser-test');

    $page->page()->context()->addInitScript(<<<'JS'
        const originalFetch = window.fetch;
        let hasDelayed = false;
        window.addEventListener('lazy-loaded-state-advanced', event => {
            document.querySelector('[data-testid="lazy-loaded-state-note"]').setAttribute('data-revision', event.detail.revision);
        });
        window.fetch = async (...arguments) => {
            const response = await originalFetch(...arguments);
            const request = arguments[1]?.body ? JSON.parse(arguments[1].body) : null;
            const call = request?.components?.[0]?.calls?.[0];
            if (call?.method !== 'loadTableColumnStates') return response;
            const target = Object.values(call.params[0])[0];
            if (target.column !== 'individual' || target.record !== '11' || hasDelayed) return response;
            hasDelayed = true;
            const content = await response.text();
            await new Promise(resolve => { window.releaseLazyLoadedStateResponse = resolve; });
            return new Response(content, { status: response.status, headers: response.headers });
        };
        JS);
    $page->refresh();

    $page->assertSee('Individual Beta v1')
        ->assertSee('Independent entry v1')
        ->assertDontSee('Individual Alpha v1')
        ->click('Advance state')
        ->assertAttribute('[data-testid="lazy-loaded-state-note"]', 'data-revision', '2');

    $page->script('window.releaseLazyLoadedStateResponse()');

    $page->assertSee('Individual Alpha v2')
        ->assertDontSee('Individual Alpha v1')
        ->click('Advance state')
        ->assertAttribute('[data-testid="lazy-loaded-state-note"]', 'data-revision', '3')
        ->click('Refresh state')
        ->assertSee('Individual Alpha v4')
        ->assertSee('Independent entry v4')
        ->assertNoSmoke();
});
