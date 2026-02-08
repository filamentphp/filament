<?php

use Filament\Actions\AttachAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithAttachActionRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithModifiedAttachQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithPreloadedAttachRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithRecordSelectSearchColumnsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;

uses(TestCase::class);

it('can render `AttachAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsWithAttachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(AttachAction::class)->table());
});

it('can mount `AttachAction` modal', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsWithAttachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertActionMounted(TestAction::make(AttachAction::class)->table());
});

it('can attach a record using `AttachAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();

    livewire(DepartmentsWithAttachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(AttachAction::class)->table(), [
            'recordId' => $department->getKey(),
        ])
        ->assertHasNoFormErrors();

    assertDatabaseHas('department_ticket', [
        'department_id' => $department->getKey(),
        'ticket_id' => $ticket->getKey(),
    ]);
});

it('can attach multiple records using `AttachAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->create();

    livewire(DepartmentsWithPreloadedAttachRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(AttachAction::class)->table(), [
            'recordId' => $departments->pluck('id')->all(),
        ])
        ->assertHasNoFormErrors();

    foreach ($departments as $department) {
        assertDatabaseHas('department_ticket', [
            'department_id' => $department->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    }
});

it('can show success notification after attaching a record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();

    livewire(DepartmentsWithAttachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(AttachAction::class)->table(), [
            'recordId' => $department->getKey(),
        ])
        ->assertNotified();
});

it('shows attached record in table', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();

    livewire(DepartmentsWithAttachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertCanNotSeeTableRecords([$department])
        ->callAction(TestAction::make(AttachAction::class)->table(), [
            'recordId' => $department->getKey(),
        ])
        ->assertCanSeeTableRecords([$department]);
});

it('can get `getOptions()` for record select with preload', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->create();

    livewire(DepartmentsWithPreloadedAttachRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($departments): bool {
            $options = $select->getOptions();

            expect($options)->toHaveCount(3);
            expect(array_values($options))->toContain($departments[0]->name);
            expect(array_values($options))->toContain($departments[1]->name);
            expect(array_values($options))->toContain($departments[2]->name);

            return true;
        });
});

it('returns empty array for `getOptions()` when not preloaded', function (): void {
    $ticket = Ticket::factory()->create();
    Department::factory()->count(3)->create();

    livewire(DepartmentsWithAttachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            expect($select->getOptions())->toBe([]);

            return true;
        });
});

it('can get `getSearchResults()` for record select', function (): void {
    $ticket = Ticket::factory()->create();
    Department::factory()->create(['name' => 'Engineering']);
    Department::factory()->create(['name' => 'Marketing']);
    Department::factory()->create(['name' => 'Sales Engineering']);

    livewire(DepartmentsWithAttachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            $results = $select->getSearchResults('Engineering');

            expect($results)->toHaveCount(2);
            expect(array_values($results))->toContain('Engineering');
            expect(array_values($results))->toContain('Sales Engineering');
            expect(array_values($results))->not->toContain('Marketing');

            return true;
        });
});

it('excludes already attached records from options', function (): void {
    $ticket = Ticket::factory()->create();
    $attachedDepartment = Department::factory()->create(['name' => 'Already Attached']);
    $availableDepartment = Department::factory()->create(['name' => 'Available']);

    $ticket->departments()->attach($attachedDepartment);

    livewire(DepartmentsWithPreloadedAttachRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            $options = $select->getOptions();

            expect($options)->toHaveCount(1);
            expect(array_values($options))->toContain('Available');
            expect(array_values($options))->not->toContain('Already Attached');

            return true;
        });
});

it('excludes already attached records from search results', function (): void {
    $ticket = Ticket::factory()->create();
    $attachedDepartment = Department::factory()->create(['name' => 'Attached Department']);
    $availableDepartment = Department::factory()->create(['name' => 'Available Department']);

    $ticket->departments()->attach($attachedDepartment);

    livewire(DepartmentsWithAttachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            $results = $select->getSearchResults('Department');

            expect($results)->toHaveCount(1);
            expect(array_values($results))->toContain('Available Department');
            expect(array_values($results))->not->toContain('Attached Department');

            return true;
        });
});

it('can get `getOptionLabel()` for selected record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create(['name' => 'Test Department']);

    livewire(DepartmentsWithAttachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->fillForm(['recordId' => $department->id])
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($department): bool {
            expect($select->getOptionLabel())->toBe($department->name);

            return true;
        });
});

it('can get `getOptionLabels()` for multiple selected records', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(2)->create();

    livewire(DepartmentsWithPreloadedAttachRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->fillForm(['recordId' => $departments->pluck('id')->all()])
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($departments): bool {
            $labels = $select->getOptionLabels();

            expect($labels)->toHaveCount(2);
            expect(array_values($labels))->toContain($departments[0]->name);
            expect(array_values($labels))->toContain($departments[1]->name);

            return true;
        });
});

it('can use `recordSelectOptionsQuery()` to modify query', function (): void {
    $ticket = Ticket::factory()->create();
    Department::factory()->create(['name' => 'Active Engineering']);
    Department::factory()->create(['name' => 'Inactive Department']);
    Department::factory()->create(['name' => 'Active Sales']);

    livewire(DepartmentsWithModifiedAttachQueryRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            $options = $select->getOptions();

            expect($options)->toHaveCount(2);
            expect(array_values($options))->toContain('Active Engineering');
            expect(array_values($options))->toContain('Active Sales');
            expect(array_values($options))->not->toContain('Inactive Department');

            return true;
        });
});

it('uses `recordSelectSearchColumns()` when configured', function (): void {
    $ticket = Ticket::factory()->create();
    Department::factory()->create(['name' => 'Engineering Dept']);
    Department::factory()->create(['name' => 'Marketing Dept']);
    Department::factory()->create(['name' => 'Sales Dept']);

    livewire(DepartmentsWithRecordSelectSearchColumnsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            // The Select should have search columns configured
            expect($select->getSearchColumns())->toBe(['name']);

            // Search should still work via the name column
            $results = $select->getSearchResults('Engineering');

            expect($results)->toHaveCount(1);
            expect(array_values($results))->toContain('Engineering Dept');

            return true;
        });
});

it('respects `optionsLimit()` on record select', function (): void {
    $ticket = Ticket::factory()->create();
    Department::factory()->count(10)->create();

    livewire(DepartmentsWithPreloadedAttachRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
            // Default options limit is 50, and we have 10 departments
            // The options should respect the limit
            expect($select->getOptionsLimit())->toBe(50);

            return true;
        });
});
