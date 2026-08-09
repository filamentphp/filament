<?php

use Filament\Actions\Enums\ActionCallStage;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Pages\Actions;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('pausing an action', function (): void {
    it('does not call an action that pauses, and keeps it mounted', function (): void {
        $livewire = livewire(Actions::class)
            ->callAction('pause')
            ->assertNotDispatched('pause-called')
            ->assertActionMounted('pause');

        expect($livewire->instance()->pausedMountedActions)
            ->toBe([0 => ActionCallStage::PauseConditions->value]);
    });

    it('does not call the `before()` hook of an action that pauses before it', function (): void {
        livewire(Actions::class)
            ->callAction('pause')
            ->assertNotDispatched('pause-before-called')
            ->assertNotDispatched('pause-after-called')
            ->assertSet('pauseHookCallCounts', []);
    });

    it('calls an action once it no longer pauses', function (): void {
        livewire(Actions::class)
            ->callAction('pause')
            ->assertNotDispatched('pause-called')
            ->set('shouldPauseActions', false)
            ->callMountedAction()
            ->assertDispatched('pause-before-called')
            ->assertDispatched('pause-called')
            ->assertDispatched('pause-after-called')
            ->assertActionNotMounted();
    });

    it('only calls the `before()` hook once across pausing and resuming', function (): void {
        livewire(Actions::class)
            ->callAction('pause')
            ->set('shouldPauseActions', false)
            ->callMountedAction()
            ->assertSet('pauseHookCallCounts', ['pause-before' => 1]);
    });

    it('forgets that an action was paused once it is unmounted', function (): void {
        $livewire = livewire(Actions::class)
            ->callAction('pause')
            ->unmountAction();

        expect($livewire->instance()->pausedMountedActions)->toBe([]);
    });
});

describe('pausing from a lifecycle hook', function (): void {
    it('can pause from the `before()` hook', function (): void {
        $livewire = livewire(Actions::class)
            ->callAction('pause-from-before')
            ->assertSet('pauseHookCallCounts', ['pause-from-before-before' => 1])
            ->assertNotDispatched('pause-from-before-called')
            ->assertActionMounted('pause-from-before');

        expect($livewire->instance()->pausedMountedActions)
            ->toBe([0 => ActionCallStage::Before->value]);
    });

    it('does not call the `before()` hook again after it paused', function (): void {
        livewire(Actions::class)
            ->callAction('pause-from-before')
            ->set('shouldPauseActions', false)
            ->callMountedAction()
            ->assertDispatched('pause-from-before-called')
            ->assertSet('pauseHookCallCounts', ['pause-from-before-before' => 1]);
    });
});

describe('pausing an action with a schema', function (): void {
    it('validates the schema before evaluating whether to pause', function (): void {
        $livewire = livewire(Actions::class)
            ->mountAction('pause-with-schema')
            ->setActionData(['payload' => null])
            ->callMountedAction()
            ->assertHasErrors()
            ->assertActionMounted('pause-with-schema');

        // Validation failed, so the action never reached its pause conditions.
        expect($livewire->instance()->pausedMountedActions)->toBe([]);
    });

    it('keeps the schema data across pausing and resuming', function (): void {
        livewire(Actions::class)
            ->mountAction('pause-with-schema')
            ->setActionData(['payload' => 'foo'])
            ->callMountedAction()
            ->assertNotDispatched('pause-with-schema-called')
            ->set('shouldPauseActions', false)
            ->callMountedAction()
            ->assertDispatched('pause-with-schema-called', data: ['payload' => 'foo']);
    });

    it('only calls the validation hooks once across pausing and resuming', function (): void {
        livewire(Actions::class)
            ->mountAction('pause-with-schema')
            ->setActionData(['payload' => 'foo'])
            ->callMountedAction()
            ->set('shouldPauseActions', false)
            ->callMountedAction()
            ->assertDispatched('pause-with-schema-called')
            ->assertSet('pauseHookCallCounts', [
                'pause-with-schema-before-form-validated' => 1,
                'pause-with-schema-after-form-validated' => 1,
                'pause-with-schema-before' => 1,
            ]);
    });
});

describe('pausing a bulk action', function (): void {
    it('keeps the selected records across pausing and resuming', function (): void {
        $posts = Post::factory()->count(3)->create();

        $livewire = livewire(PostsTable::class)
            ->selectTableRecords($posts)
            ->callAction(TestAction::make('pause')->table()->bulk())
            ->assertNotDispatched('pause-called');

        expect($livewire->instance()->isMountedActionPaused())->toBeTrue();

        $livewire
            ->set('shouldPauseActions', false)
            ->callMountedAction()
            ->assertDispatched('pause-called', count: 3);
    });
});

describe('rate limiting a paused action', function (): void {
    it('does not consume another attempt when an action is resumed', function (): void {
        // The limit is 2 attempts. Without pausing being accounted for, the pause and
        // the resumption would consume both of them, and the action would never run.
        livewire(Actions::class)
            ->callAction('rate-limited-pause')
            ->assertNotDispatched('rate-limited-pause-called')
            ->set('shouldPauseActions', false)
            ->callMountedAction()
            ->assertNotNotified('Too many attempts')
            ->assertDispatched('rate-limited-pause-called')
            ->callAction('rate-limited-pause')
            ->assertNotNotified('Too many attempts');
    });
});

describe('resuming from a nested action', function (): void {
    it('mounts the nested action that the pause condition mounts', function (): void {
        $livewire = livewire(Actions::class)
            ->callAction('pause-until-confirmed')
            ->assertNotDispatched('pause-until-confirmed-called')
            ->assertActionMounted(TestAction::make('confirm-pause')->schemaComponent(null));

        expect(array_column($livewire->instance()->mountedActions, 'name'))
            ->toBe(['pause-until-confirmed', 'confirm-pause']);
    });

    it('does not resume the parent action when the nested action fails validation', function (): void {
        livewire(Actions::class)
            ->callAction('pause-until-confirmed')
            ->setActionData(['code' => '000000'])
            ->callMountedAction()
            ->assertHasErrors()
            ->assertNotDispatched('pause-until-confirmed-called');
    });

    it('resumes the parent action when the nested action succeeds', function (): void {
        livewire(Actions::class)
            ->callAction('pause-until-confirmed')
            ->setActionData(['code' => '123456'])
            ->callMountedAction()
            ->assertDispatched('pause-until-confirmed-called')
            ->assertActionNotMounted();
    });

    it('can fill the schema of the paused action from the nested action', function (): void {
        livewire(Actions::class)
            ->mountAction('pause-until-filled')
            ->setActionData([
                'title' => 'Foo',
                'reference' => null,
            ])
            ->callMountedAction()
            ->assertNotDispatched('pause-until-filled-called')
            ->assertActionMounted(TestAction::make('generate-reference')->schemaComponent(null))
            ->setActionData(['prefix' => 'INV'])
            ->callMountedAction()
            ->assertDispatched('pause-until-filled-called', data: [
                'title' => 'Foo',
                'reference' => 'INV-123',
            ])
            ->assertActionNotMounted();
    });

    it('pauses again when the parent action is called without the nested action being confirmed', function (): void {
        // Cancelling the nested action must not leave the parent resumable, otherwise
        // the browser could call the parent action without ever satisfying the pause.
        livewire(Actions::class)
            ->callAction('pause-until-confirmed')
            ->unmountAction()
            ->callMountedAction()
            ->assertNotDispatched('pause-until-confirmed-called')
            ->assertActionMounted(TestAction::make('confirm-pause')->schemaComponent(null));
    });
});
