<?php

use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;

uses(TestCase::class);

it('can render `CreateAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(CreateAction::class)->table());
});

it('can mount `CreateAction` modal', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(CreateAction::class)->table())
        ->assertActionMounted(TestAction::make(CreateAction::class)->table());
});

it('can fill form data in `CreateAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(CreateAction::class)->table())
        ->fillForm([
            'name' => 'Test Department',
        ])
        ->assertSchemaStateSet([
            'name' => 'Test Department',
        ]);
});

it('can validate form data in `CreateAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'name' => null,
        ])
        ->assertHasFormErrors(['name' => ['required']]);
});

it('can create a record using `CreateAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'name' => $name = Str::random(),
        ])
        ->assertHasNoFormErrors();

    assertDatabaseHas(Department::class, ['name' => $name]);
});

it('can show success notification after creating a record', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'name' => Str::random(),
        ])
        ->assertNotified();
});

it('attaches created record to relationship', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(CreateAction::class)->table(), [
            'name' => $name = Str::random(),
        ]);

    $ticket->refresh();

    expect($ticket->departments)->toHaveCount(1);
    expect($ticket->departments->first()->name)->toBe($name);
});

it('can create another record using `CreateAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(CreateAction::class)->table(), ['another' => true])
        ->fillForm([
            'name' => $firstName = Str::random(),
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    assertDatabaseHas(Department::class, ['name' => $firstName]);
});

it('can cancel `CreateAction` without creating record', function (): void {
    $ticket = Ticket::factory()->create();
    $initialCount = Department::count();

    livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(CreateAction::class)->table())
        ->fillForm([
            'name' => Str::random(),
        ])
        ->assertActionMounted(TestAction::make(CreateAction::class)->table());

    expect(Department::count())->toBe($initialCount);
});
