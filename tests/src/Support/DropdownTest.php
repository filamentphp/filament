<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('does not loop on `aria-controls` when a Livewire morph initialises the dropdown a second time', function (): void {
    // Counts every `aria-controls` write on a dropdown trigger and every `id` write on a
    // dropdown panel, and remembers each row's Alpine marker. Above the limit the write is
    // swallowed, so a looping page stays responsive and can still report instead of
    // freezing the renderer. It must run before the first morph: `Alpine.morph()` wraps
    // `Element.prototype.setAttribute` itself on first use, and that wrapper has to sit on
    // top of this one for the writes to still pass through here.
    $installCounter = <<<'JS'
        const LIMIT = 5000;
        const state = { writes: 0, limitHit: false };

        const setAttribute = Element.prototype.setAttribute;
        Element.prototype.setAttribute = function (name, value) {
            if (name === 'aria-controls' && this.closest?.('.fi-dropdown-trigger')) {
                state.writes++;
                if (state.writes > LIMIT) { state.limitHit = true; return; }
            }
            return Reflect.apply(setAttribute, this, [name, value]);
        };

        const id = Object.getOwnPropertyDescriptor(Element.prototype, 'id');
        Object.defineProperty(Element.prototype, 'id', {
            configurable: true,
            get: id.get,
            set(value) {
                if (this.classList?.contains('fi-dropdown-panel')) {
                    state.writes++;
                    if (state.writes > LIMIT) { state.limitHit = true; return; }
                }
                return Reflect.apply(id.set, this, [value]);
            },
        });

        state.markers = {};
        document.querySelectorAll('li[wire\\:key] .fi-dropdown').forEach((dropdown) => {
            state.markers[dropdown.closest('li').getAttribute('wire:key')] = dropdown._x_marker;
        });

        window.dropdownLoop = state;
    JS;

    $report = <<<'JS'
        (() => {
            const state = window.dropdownLoop;
            let reinitialisedRows = 0;

            document.querySelectorAll('li[wire\\:key] .fi-dropdown').forEach((dropdown) => {
                const marker = state.markers[dropdown.closest('li').getAttribute('wire:key')];
                if (marker !== undefined && marker !== dropdown._x_marker) reinitialisedRows++;
            });

            return JSON.stringify({ reinitialisedRows, writes: state.writes, limitHit: state.limitHit });
        })()
    JS;

    retry(10, function () use ($installCounter, $report): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/dropdown-browser-test')
            ->assertSee('item-1');

        $page->script($installCounter);

        // Prepending to a keyed list makes the morph move every retained row: each one is
        // removed and re-inserted, not recreated. Each row also has an `x-text` binding,
        // whose effect runs `mutateDom` while the morph clones the incoming row, which
        // flushes Alpine's mutation observer mid-morph. The removal and re-insertion of a
        // row then land in different batches, so Alpine runs `destroyTree` + `initTree`
        // on the same element, and each dropdown gets a second instance.
        $page->click('[data-testid="prepend"]')
            ->waitForText('new-1');

        // The second instance may generate its own panel `id` while the first instance's
        // observer, never disconnected, still holds the old one. The next morph strips
        // `aria-controls` again, both observers re-apply their own panel `id` to it, and
        // they trigger each other until the renderer dies.
        $page->click('[data-testid="prepend"]')
            ->waitForText('new-2')
            ->wait(1);

        $result = json_decode($page->script($report), true, flags: JSON_THROW_ON_ERROR);

        // Without a second initialisation the assertions below would prove nothing.
        expect($result['reinitialisedRows'])->toBeGreaterThan(0, 'the morph did not initialise any row a second time: ' . json_encode($result));

        expect($result['limitHit'])->toBeFalse('`aria-controls` kept ping-ponging: ' . json_encode($result));
        expect($result['writes'])->toBeLessThan(500, json_encode($result));

        $page->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/dropdown-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});
