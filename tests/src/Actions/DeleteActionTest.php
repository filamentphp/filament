<?php

use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can render `DeleteAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(DeleteAction::class)->table($department));
});

it('can mount `DeleteAction` confirmation modal', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(DeleteAction::class)->table($department))
        ->assertActionMounted(TestAction::make(DeleteAction::class)->table($department));
});

it('can soft delete a record using `DeleteAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(DeleteAction::class)->table($department));

    assertSoftDeleted($department);
});

it('can show success notification after deleting a record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(DeleteAction::class)->table($department))
        ->assertNotified();
});

it('removes record from table after deletion', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertCanSeeTableRecords([$department])
        ->callAction(TestAction::make(DeleteAction::class)->table($department))
        ->assertCanNotSeeTableRecords([$department]);
});

it('can delete multiple records sequentially', function (): void {
    $ticket = Ticket::factory()->create();
    $department1 = Department::factory()->hasAttached($ticket)->create();
    $department2 = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(DeleteAction::class)->table($department1))
        ->callAction(TestAction::make(DeleteAction::class)->table($department2));

    assertSoftDeleted($department1);
    assertSoftDeleted($department2);
});

it('returns `delete` from `getDefaultName()`', function (): void {
    expect(DeleteAction::getDefaultName())->toBe('delete');
});

it('has danger color by default', function (): void {
    $action = DeleteAction::make();

    expect($action->getColor())->toBe('danger');
});

it('requires confirmation by default', function (): void {
    $action = DeleteAction::make();

    expect($action->isConfirmationRequired())->toBeTrue();
});

it('can set custom process via `using()`', function (): void {
    $action = DeleteAction::make()
        ->using(static fn (): string => 'custom-result');

    expect($action)->toBeInstanceOf(DeleteAction::class);
});

it('returns fluent `$this` from `using()`', function (): void {
    $action = DeleteAction::make();

    expect($action->using(static fn () => null))->toBe($action);
});
