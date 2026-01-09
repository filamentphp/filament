<?php

use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render `EditAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(EditAction::class)->table($department));
});

it('can mount `EditAction` modal', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(EditAction::class)->table($department))
        ->assertActionMounted(TestAction::make(EditAction::class)->table($department));
});

it('can fill form with record data in `EditAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original Name']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(EditAction::class)->table($department))
        ->assertSchemaStateSet([
            'name' => 'Original Name',
        ]);
});

it('can validate form data in `EditAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(EditAction::class)->table($department), [
            'name' => null,
        ])
        ->assertHasFormErrors(['name' => ['required']]);
});

it('can update a record using `EditAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original Name']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(EditAction::class)->table($department), [
            'name' => $newName = Str::random(),
        ])
        ->assertHasNoFormErrors();

    expect($department->refresh()->name)->toBe($newName);
});

it('can show success notification after updating a record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(EditAction::class)->table($department), [
            'name' => Str::random(),
        ])
        ->assertNotified();
});

it('does not update record when `EditAction` is cancelled', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create(['name' => 'Original Name']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(EditAction::class)->table($department))
        ->fillForm([
            'name' => 'Changed Name',
        ])
        ->assertActionMounted(TestAction::make(EditAction::class)->table($department));

    expect($department->refresh()->name)->toBe('Original Name');
});

it('can edit multiple records sequentially', function (): void {
    $ticket = Ticket::factory()->create();
    $department1 = Department::factory()->hasAttached($ticket)->create(['name' => 'Department 1']);
    $department2 = Department::factory()->hasAttached($ticket)->create(['name' => 'Department 2']);

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(EditAction::class)->table($department1), [
            'name' => $newName1 = Str::random(),
        ])
        ->assertHasNoFormErrors()
        ->callAction(TestAction::make(EditAction::class)->table($department2), [
            'name' => $newName2 = Str::random(),
        ])
        ->assertHasNoFormErrors();

    expect($department1->refresh()->name)->toBe($newName1);
    expect($department2->refresh()->name)->toBe($newName2);
});
