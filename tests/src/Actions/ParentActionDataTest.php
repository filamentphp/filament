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
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
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

    it('does not call `beforeStateDehydrated()` when `getValidatedData()` reads', function (): void {
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
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
            ])
            ->assertSet('parentDataDehydrationCount', 0);
    });

    it('calls `beforeStateDehydrated()` when the parent action is submitted', function (): void {
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
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
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

describe('reading a parent action from an action that is not registered on its modal', function (): void {
    it('can use `getParentActionValidatedData()` from an action registered on a schema component', function (): void {
        livewire(Actions::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('readParentDataFromComponent')->schemaComponent('reference'))
            ->assertDispatched('read-parent-data', data: [
                'payload' => 'foo',
                'reference' => 'bar',
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
            ]);
    });

    it('throws from `getParentActionValidatedData()` when the action was not mounted from a parent action', function (): void {
        livewire(Actions::class)
            ->callAction('readWithoutParent')
            ->assertNotDispatched('read-without-parent-called');
    })->throws(LogicException::class);
});

describe('filling a parent action from an action that is not registered on its modal', function (): void {
    it('can use `fillParentActionData()` from an action registered on a schema component', function (): void {
        livewire(Actions::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => null,
            ])
            ->callAction(TestAction::make('fillParentDataFromComponent')->schemaComponent('reference'))
            ->callMountedAction()
            ->assertHasNoErrors()
            ->assertDispatched('parent-data-called', data: [
                'payload' => 'foo',
                'reference' => 'from component',
                'nested' => ['city' => null],
                'dotted' => ['postcode' => null],
            ]);
    });

    it('throws when the action was not mounted from a parent action', function (): void {
        livewire(Actions::class)
            ->callAction('fillWithoutParent')
            ->assertNotDispatched('fill-without-parent-called');
    })->throws(LogicException::class);
});

describe('filling nested state of a parent action', function (): void {
    it('writes nested state and dot-notation keys where the parent action reads them', function (): void {
        livewire(Actions::class)
            ->mountAction('parentData')
            ->setActionData([
                'payload' => 'foo',
                'reference' => 'bar',
            ])
            ->callAction(TestAction::make('fillParentDataWithNesting'))
            ->callMountedAction()
            ->assertHasNoErrors()
            ->assertDispatched('parent-data-called', data: [
                'payload' => 'foo',
                'reference' => 'bar',
                'nested' => ['city' => 'generated city'],
                'dotted' => ['postcode' => 'generated postcode'],
            ]);
    });
});
