<?php

use Filament\Actions\Testing\TestAction;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Pages\Actions;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('reading the data of a parent action', function (): void {
    it('can use `getValidatedData()` to read the validated data of a parent action', function (): void {
        livewire(Actions::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('readParentData'))
            ->assertDispatched('read-parent-data', data: [
                'payload' => 'foo',
                'reference' => 'bar',
            ]);
    });

    it('throws a validation exception from `getValidatedData()` when the parent action is invalid', function (): void {
        livewire(Actions::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => null,
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('readParentData'))
            ->assertHasErrors()
            ->assertNotDispatched('read-parent-data');
    });

    it('does not call the hooks that write when `getValidatedData()` reads', function (): void {
        livewire(Actions::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('readParentData'))
            ->assertDispatched('read-parent-data')
            ->assertSet('parentDataDehydrationCount', 0);
    });

    it('calls the hooks that write when the parent action is submitted', function (): void {
        livewire(Actions::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callMountedAction()
            ->assertDispatched('parent-data-called')
            ->assertSet('parentDataDehydrationCount', 1);
    });
});

describe('filling the data of a parent action', function (): void {
    it('can use `fillParentActionData()` to write into a parent action', function (): void {
        livewire(Actions::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => null,
            ])
            ->callAction(TestAction::make('fillParentData'))
            ->callMountedAction()
            ->assertHasNoErrors()
            ->assertDispatched('parent-data-called', data: [
                'payload' => 'foo',
                'reference' => 'generated',
            ]);
    });

    it('validates what was written with the rules of the parent action', function (): void {
        livewire(Actions::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => null,
            ])
            ->callMountedAction()
            ->assertHasErrors(['mountedActions.0.data.reference'])
            ->assertNotDispatched('parent-data-called');
    });
});
