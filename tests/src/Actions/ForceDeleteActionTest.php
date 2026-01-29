<?php

use Filament\Actions\ForceDeleteAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can render `ForceDeleteAction` on soft deleted record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->assertActionExists(TestAction::make(ForceDeleteAction::class)->table($department));
});

it('can mount `ForceDeleteAction` confirmation modal', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->mountAction(TestAction::make(ForceDeleteAction::class)->table($department))
        ->assertActionMounted(TestAction::make(ForceDeleteAction::class)->table($department));
});

it('can permanently delete a soft deleted record using `ForceDeleteAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    assertSoftDeleted($department);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callAction(TestAction::make(ForceDeleteAction::class)->table($department));

    assertDatabaseMissing('departments', ['id' => $department->getKey()]);
});

it('can show success notification after force deleting a record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callAction(TestAction::make(ForceDeleteAction::class)->table($department))
        ->assertNotified();
});

it('removes record from table after force deletion', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->assertCanSeeTableRecords([$department])
        ->callAction(TestAction::make(ForceDeleteAction::class)->table($department))
        ->assertCanNotSeeTableRecords([$department]);
});

it('can force delete multiple records sequentially', function (): void {
    $ticket = Ticket::factory()->create();
    $department1 = Department::factory()->hasAttached($ticket)->create();
    $department2 = Department::factory()->hasAttached($ticket)->create();
    $department1->delete();
    $department2->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callAction(TestAction::make(ForceDeleteAction::class)->table($department1))
        ->callAction(TestAction::make(ForceDeleteAction::class)->table($department2));

    assertDatabaseMissing('departments', ['id' => $department1->getKey()]);
    assertDatabaseMissing('departments', ['id' => $department2->getKey()]);
});
