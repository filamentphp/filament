<?php

use Filament\Actions\RestoreAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;

uses(TestCase::class);

it('can render `RestoreAction` on soft deleted record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->assertActionExists(TestAction::make(RestoreAction::class)->table($department));
});

it('can mount `RestoreAction` confirmation modal', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->mountAction(TestAction::make(RestoreAction::class)->table($department))
        ->assertActionMounted(TestAction::make(RestoreAction::class)->table($department));
});

it('can restore a soft deleted record using `RestoreAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    assertSoftDeleted($department);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callAction(TestAction::make(RestoreAction::class)->table($department));

    assertNotSoftDeleted($department);
});

it('can show success notification after restoring a record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callAction(TestAction::make(RestoreAction::class)->table($department))
        ->assertNotified();
});

it('makes record available in active table view after restoration', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $department->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->assertCanSeeTableRecords([$department])
        ->callAction(TestAction::make(RestoreAction::class)->table($department));

    // Verify record is no longer soft deleted
    assertNotSoftDeleted($department);

    // Verify record is visible when viewing all records (not filtering for trashed)
    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertCanSeeTableRecords([$department]);
});

it('can restore multiple records sequentially', function (): void {
    $ticket = Ticket::factory()->create();
    $department1 = Department::factory()->hasAttached($ticket)->create();
    $department2 = Department::factory()->hasAttached($ticket)->create();
    $department1->delete();
    $department2->delete();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callAction(TestAction::make(RestoreAction::class)->table($department1))
        ->callAction(TestAction::make(RestoreAction::class)->table($department2));

    assertNotSoftDeleted($department1);
    assertNotSoftDeleted($department2);
});
