<?php

use Filament\Actions\RestoreBulkAction;
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

it('can render `RestoreBulkAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(RestoreBulkAction::class)->table()->bulk());
});

it('can mount `RestoreBulkAction` confirmation modal', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->hasAttached($ticket)->create();
    foreach ($departments as $department) {
        $department->delete();
    }

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->selectTableRecords($departments)
        ->mountAction(TestAction::make(RestoreBulkAction::class)->table()->bulk())
        ->assertActionMounted(TestAction::make(RestoreBulkAction::class)->table()->bulk());
});

it('can restore selected soft deleted records using `RestoreBulkAction`', function (): void {
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
        ->callTableBulkAction(RestoreBulkAction::class, $departments);

    foreach ($departments as $department) {
        assertNotSoftDeleted($department);
    }
});

it('can show success notification after restoring records', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(2)->hasAttached($ticket)->create();
    foreach ($departments as $department) {
        $department->delete();
    }

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->filterTable('trashed', 1)
        ->callTableBulkAction(RestoreBulkAction::class, $departments)
        ->assertNotified();
});

it('only restores selected records', function (): void {
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
        ->callTableBulkAction(RestoreBulkAction::class, $selectedDepartments);

    foreach ($selectedDepartments as $department) {
        assertNotSoftDeleted($department);
    }

    foreach ($unselectedDepartments as $department) {
        assertSoftDeleted($department);
    }
});
