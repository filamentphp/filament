<?php

use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
use Filament\Tests\Fixtures\Livewire\ScopedTicketDepartmentsTable;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render `ViewAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(ViewAction::class)->table($department));
});

it('can mount `ViewAction` modal', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department))
        ->assertActionMounted(TestAction::make(ViewAction::class)->table($department));
});

it('can display record data in `ViewAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Test Department']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department))
        ->assertSchemaStateSet([
            'name' => 'Test Department',
        ]);
});

it('displays form as disabled in `ViewAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Test Department']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department))
        ->assertSchemaComponentExists('name', checkComponentUsing: function ($component): bool {
            return $component->isDisabled();
        });
});

it('does not prefill hidden pivot attributes or their related-column collisions', function (bool $isTranslatable): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();
    $ticket->departments()->attach($department, ['quantity' => 7, 'price' => 70]);

    expect($department->attributesToArray())->toHaveKey('created_at');

    livewire(ScopedTicketDepartmentsTable::class, ['ticketKey' => $ticket->getKey(), 'hasHiddenPivot' => true, 'isTranslatable' => $isTranslatable])
        ->mountAction(TestAction::make('view')->table($department))
        ->assertSchemaStateSet(['quantity' => null, 'pivot_quantity' => null, 'created_at' => null, 'pivot_created_at' => null, 'price' => 70]);
})->with([false, true]);

it('can view multiple records sequentially', function (): void {
    $ticket = Ticket::factory()->create();
    $department1 = Department::factory()->hasAttached($ticket)->create(['name' => 'Department 1']);
    $department2 = Department::factory()->hasAttached($ticket)->create(['name' => 'Department 2']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department1))
        ->assertSchemaStateSet(['name' => 'Department 1'])
        ->unmountAction()
        ->mountAction(TestAction::make(ViewAction::class)->table($department2))
        ->assertSchemaStateSet(['name' => 'Department 2']);
});

it('does not flag a mounted `ViewAction` for the unsaved changes alert', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(ViewAction::class)->table($department))
        ->assertSet('mountedActions.0.hasUnsavedChangesAlert', false, strict: true);
});

it('returns `view` from `getDefaultName()`', function (): void {
    expect(ViewAction::getDefaultName())->toBe('view');
});
