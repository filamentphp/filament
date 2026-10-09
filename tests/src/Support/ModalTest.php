<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('does not run queued `init()` or `open()` work after modal teardown', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $browser = visit('/modal-browser-test');

    foreach ([false, true] as $isDarkMode) {
        if ($isDarkMode) {
            $browser->inDarkMode();
        }

        $results = $browser->script(<<<'JS'
            (async () => {
                const results = []
                const original = document.querySelector('[data-testid="standalone-modal"]').closest('[data-fi-modal-id]')
                const trigger = document.querySelector('[data-testid="standalone-trigger"]')

                for (const teardown of ['destroy-connected', 'destroy-removed', 'disconnected']) {
                    for (const shouldSettleInitialization of [false, true]) {
                        if ((teardown === 'disconnected') && shouldSettleInitialization) {
                            continue
                        }

                        const element = original.cloneNode(true)
                        element.id = 'modal-lifecycle-test'

                        Alpine.mutateDom(() => {
                            document.body.append(element)
                            Alpine.initTree(element)
                        })

                        const state = Alpine.$data(element)

                        if (shouldSettleInitialization) {
                            await Alpine.nextTick()
                        }

                        let openedEventCount = 0
                        const openedHandler = () => openedEventCount++
                        document.addEventListener('x-modal-opened', openedHandler)

                        trigger.focus({ preventScroll: true })
                        const overflow = document.documentElement.style.overflow
                        const paddingRight = document.documentElement.style.paddingRight

                        state.open()

                        Alpine.mutateDom(() => {
                            if (teardown !== 'disconnected') {
                                Alpine.destroyTree(element)
                            }

                            if (teardown !== 'destroy-connected') {
                                element.remove()
                            }
                        })

                        await Alpine.nextTick()

                        results.push({
                            isOpen: state.isOpen,
                            isTrapActive: state.isTrapActive,
                            isHoldingScrollLock: state.isHoldingScrollLock,
                            hasSelectionHandlers: !!(state.textSelectionClosePreventionMouseDownHandler || state.textSelectionClosePreventionMouseUpHandler || state.textSelectionClosePreventionClickHandler),
                            openedEventCount,
                            isScrollRestored: document.documentElement.style.overflow === overflow && document.documentElement.style.paddingRight === paddingRight,
                            isFocusPreserved: document.activeElement === trigger,
                        })

                        document.removeEventListener('x-modal-opened', openedHandler)

                        Alpine.mutateDom(() => {
                            Alpine.destroyTree(element)
                            element.remove()
                        })
                    }
                }

                return results
            })()
            JS);

        expect($results)->toHaveCount(5);

        foreach ($results as $result) {
            expect($result)->toBe([
                'isOpen' => false,
                'isTrapActive' => false,
                'isHoldingScrollLock' => false,
                'hasSelectionHandlers' => false,
                'openedEventCount' => 0,
                'isScrollRestored' => true,
                'isFocusPreserved' => true,
            ]);
        }

        $browser
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});
