<?php

use Filament\Actions\DetachBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithDetachBulkActionRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(TestCase::class);

it('can render `DetachBulkAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsWithDetachBulkActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(DetachBulkAction::class)->table()->bulk());
});

it('can mount `DetachBulkAction` confirmation modal', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachBulkActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->selectTableRecords($departments)
        ->mountAction(TestAction::make(DetachBulkAction::class)->table()->bulk())
        ->assertActionMounted(TestAction::make(DetachBulkAction::class)->table()->bulk());
});

it('can detach selected records using `DetachBulkAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachBulkActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callTableBulkAction(DetachBulkAction::class, $departments);

    foreach ($departments as $department) {
        assertDatabaseMissing('department_ticket', [
            'department_id' => $department->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    }
});

it('does not delete records when detaching', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachBulkActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callTableBulkAction(DetachBulkAction::class, $departments);

    foreach ($departments as $department) {
        assertDatabaseHas('departments', ['id' => $department->getKey()]);
    }
});

it('can show success notification after detaching records', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(2)->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachBulkActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callTableBulkAction(DetachBulkAction::class, $departments)
        ->assertNotified();
});

it('only detaches selected records', function (): void {
    $ticket = Ticket::factory()->create();
    $selectedDepartments = Department::factory()->count(2)->hasAttached($ticket)->create();
    $unselectedDepartments = Department::factory()->count(2)->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachBulkActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callTableBulkAction(DetachBulkAction::class, $selectedDepartments);

    foreach ($selectedDepartments as $department) {
        assertDatabaseMissing('department_ticket', [
            'department_id' => $department->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    }

    foreach ($unselectedDepartments as $department) {
        assertDatabaseHas('department_ticket', [
            'department_id' => $department->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    }
});
