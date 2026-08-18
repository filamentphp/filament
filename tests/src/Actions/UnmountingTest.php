<?php

use Filament\Actions\Testing\TestAction;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Pages\Actions;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('an action that cannot open a modal', function (): void {
    it('does not stay mounted when it halts', function (): void {
        $livewire = livewire(Actions::class)
            ->callAction('haltsWithoutModal')
            ->assertDispatched('halts-without-modal-called');

        expect($livewire->instance()->mountedActions)->toBe([]);
    });

    it('does not stay mounted once the modal it opened is closed', function (): void {
        $livewire = livewire(Actions::class)
            ->callAction('haltsWithoutModalAfterMountingAChild');

        expect(array_column($livewire->instance()->mountedActions, 'name'))
            ->toBe(['haltsWithoutModalAfterMountingAChild', 'childOfActionWithoutModal']);

        // `false` is what a modal sends when it is closed, unless the action opts into
        // cancelling its parents with `cancelParentActionsOnClose()`.
        $livewire->unmountAction(false);

        expect($livewire->instance()->mountedActions)->toBe([]);
    });

    it('does not stop other actions from being used once the modal it opened is closed', function (): void {
        livewire(Actions::class)
            ->callAction('haltsWithoutModalAfterMountingAChild')
            ->unmountAction(false)
            ->callAction('simple')
            ->assertDispatched('simple-called');
    });

    it('does not take a mounted parent that can open a modal with it', function (): void {
        $livewire = livewire(Actions::class)
            ->mountAction(['hasModalAndMountsAnActionWithoutModal', 'nestedActionWithoutModal']);

        expect(array_column($livewire->instance()->mountedActions, 'name'))
            ->toBe(['hasModalAndMountsAnActionWithoutModal', 'nestedActionWithoutModal', 'childOfNestedActionWithoutModal']);

        $livewire->unmountAction(false);

        // Unmounting stops at the grandparent, which has a modal to be seen in, so the user
        // returns to it rather than losing the whole flow.
        expect(array_column($livewire->instance()->mountedActions, 'name'))
            ->toBe(['hasModalAndMountsAnActionWithoutModal']);
    });
});

describe('an action that can open a modal', function (): void {
    // Documented behaviour: closing a child's modal leaves its parents mounted, so the
    // user returns to the parent's modal rather than losing the whole flow.
    it('stays mounted once the modal it opened is closed', function (): void {
        $livewire = livewire(Actions::class)
            ->mountAction('haltsWithModalAfterMountingAChild')
            ->callMountedAction();

        expect(array_column($livewire->instance()->mountedActions, 'name'))
            ->toBe(['haltsWithModalAfterMountingAChild', 'childOfActionWithModal']);

        $livewire->unmountAction(false);

        expect(array_column($livewire->instance()->mountedActions, 'name'))
            ->toBe(['haltsWithModalAfterMountingAChild']);
    });

    // Documented behaviour: `halt()` keeps the modal open, and `cancel()` is what closes
    // it, so a halted action with a modal has to stay mounted.
    it('stays mounted when it halts', function (): void {
        $livewire = livewire(Actions::class)
            ->callAction('halt')
            ->assertDispatched('halt-called')
            ->assertActionMounted(TestAction::make('halt'));

        expect(array_column($livewire->instance()->mountedActions, 'name'))
            ->toBe(['halt']);
    });
});
