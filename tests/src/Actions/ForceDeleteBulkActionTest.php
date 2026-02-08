<?php

use Filament\Actions\ForceDeleteBulkAction;
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

it('can render `ForceDeleteBulkAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(ForceDeleteBulkAction::class)->table()->bulk());
});

it('can mount `ForceDeleteBulkAction` confirmation modal', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->hasAttached($ticket)->create();
    foreach ($departments as $department) {
        $department->delete();
    }

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->selectTableRecords($departments)
        ->mountAction(TestAction::make(ForceDeleteBulkAction::class)->table()->bulk())
        ->assertActionMounted(TestAction::make(ForceDeleteBulkAction::class)->table()->bulk());
});

it('can permanently delete selected soft deleted records using `ForceDeleteBulkAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->hasAttached($ticket)->create();
    foreach ($departments as $department) {
        $department->delete();
    }

    foreach ($departments as $department) {
        assertSoftDeleted($department);
    }

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callTableBulkAction(ForceDeleteBulkAction::class, $departments);

    foreach ($departments as $department) {
        assertDatabaseMissing('departments', ['id' => $department->getKey()]);
    }
});

it('can show success notification after force deleting records', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(2)->hasAttached($ticket)->create();
    foreach ($departments as $department) {
        $department->delete();
    }

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callTableBulkAction(ForceDeleteBulkAction::class, $departments)
        ->assertNotified();
});

it('only force deletes selected records', function (): void {
    $ticket = Ticket::factory()->create();
    $selectedDepartments = Department::factory()->count(2)->hasAttached($ticket)->create();
    $unselectedDepartments = Department::factory()->count(2)->hasAttached($ticket)->create();

    foreach ([$selectedDepartments, $unselectedDepartments] as $departments) {
        foreach ($departments as $department) {
            $department->delete();
        }
    }

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callTableBulkAction(ForceDeleteBulkAction::class, $selectedDepartments);

    foreach ($selectedDepartments as $department) {
        assertDatabaseMissing('departments', ['id' => $department->getKey()]);
    }

    foreach ($unselectedDepartments as $department) {
        assertSoftDeleted($department);
    }
});
