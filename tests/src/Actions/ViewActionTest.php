<?php

use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
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
