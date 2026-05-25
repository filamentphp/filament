<?php

use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Policies\DepartmentPolicy;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManagerWithTabs;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithAttachTableSelectAndModifiedQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithAttachTableSelectRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithDeferredBadgeRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithMixedSummaryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithModifiedQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithPivotAndModifiedQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithPivotSummaryRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(TestCase::class);

describe('rendering and authorization', function (): void {
    it('can render relation manager', function (): void {
        $ticket = Ticket::factory()
            ->create();

        livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->assertSuccessful();
    });

    it('can list departments', function (): void {
        $ticket = Ticket::factory()
            ->hasAttached(Department::factory(10))
            ->create();

        livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->assertCanSeeTableRecords($ticket->departments);
    });

    it('can render relation manager if the policy viewAny returns true', function (): void {
        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);

        $ticket = Ticket::factory()
            ->create();

        expect(
            DepartmentsRelationManager::canViewForRecord($ticket, EditTicket::class),
        )->toBeTrue();
    });

    it('can render relation manager if the policy viewAny returns an allowed response', function (): void {
        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): Response => Response::allow());

        $ticket = Ticket::factory()
            ->create();

        expect(
            DepartmentsRelationManager::canViewForRecord($ticket, EditTicket::class),
        )->toBeTrue();

        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);
    });

    it('does not render relation manager if the policy viewAny returns false', function (): void {
        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => false);

        $ticket = Ticket::factory()
            ->create();

        expect(
            DepartmentsRelationManager::canViewForRecord($ticket, EditTicket::class),
        )->toBeFalse();

        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);
    });

    it('does not render relation manager if the policy viewAny returns a denied response', function (): void {
        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): Response => Response::deny());

        $ticket = Ticket::factory()
            ->create();

        expect(
            DepartmentsRelationManager::canViewForRecord($ticket, EditTicket::class),
        )->toBeFalse();

        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);
    });

    it('re-authorizes the relation manager on Livewire updates after the initial mount', function (): void {
        $ticket = Ticket::factory()
            ->create();

        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);

        $component = livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class]);

        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => false);

        $component
            ->set('tableSearch', 'foo')
            ->assertStatus(403);

        app()->bind(DepartmentPolicy::class . '::viewAny', fn (): bool => true);
    });

    it('renders actions based on policy', function (string $action, string $policyMethod, bool | Response $policyResult, bool $isVisible, bool $isSoftDeleted = false, bool $isBulkAction = false): void {
        app()->bind(DepartmentPolicy::class . '::' . $policyMethod, fn (): bool | Response => $policyResult);

        $ticket = Ticket::factory()
            ->create();

        $department = Department::factory()
            ->hasAttached($ticket)
            ->create();

        if ($isSoftDeleted) {
            $department->delete();
        }

        livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->filterTable('trashed', $isSoftDeleted ? 1 : null)
            ->{$isVisible ? 'assertActionVisible' : 'assertActionHidden'}(TestAction::make($action)->table($department)->bulk($isBulkAction));

        app()->bind(DepartmentPolicy::class . '::' . $policyMethod, fn (): bool => true);
    })->with([
        'create action with policy returning true' => fn (): array => [CreateAction::class, 'create', true, true],
        'create action with policy returning allowed response' => fn (): array => [CreateAction::class, 'create', Response::allow(), true],
        'create action with policy returning false' => fn (): array => [CreateAction::class, 'create', false, false],
        'create action with policy returning denied response' => fn (): array => [CreateAction::class, 'create', Response::deny(), false],
        'view action with policy returning true' => fn (): array => [ViewAction::class, 'view', true, true, false],
        'view action with policy returning allowed response' => fn (): array => [ViewAction::class, 'view', Response::allow(), true, false],
        'view action with policy returning false' => fn (): array => [ViewAction::class, 'view', false, false, false],
        'view action with policy returning denied response' => fn (): array => [ViewAction::class, 'view', Response::deny(), false, false],
        'edit action with policy returning true' => fn (): array => [EditAction::class, 'update', true, true, false],
        'edit action with policy returning allowed response' => fn (): array => [EditAction::class, 'update', Response::allow(), true, false],
        'edit action with policy returning false' => fn (): array => [EditAction::class, 'update', false, false, false],
        'edit action with policy returning denied response' => fn (): array => [EditAction::class, 'update', Response::deny(), false, false],
        'delete action with policy returning true' => fn (): array => [DeleteAction::class, 'delete', true, true, false],
        'delete action with policy returning allowed response' => fn (): array => [DeleteAction::class, 'delete', Response::allow(), true, false],
        'delete action with policy returning false' => fn (): array => [DeleteAction::class, 'delete', false, false, false],
        'delete action with policy returning denied response' => fn (): array => [DeleteAction::class, 'delete', Response::deny(), false, false],
        'force delete action with policy returning true' => fn (): array => [ForceDeleteAction::class, 'forceDelete', true, true, true],
        'force delete action with policy returning allowed response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::allow(), true, true],
        'force delete action with policy returning false' => fn (): array => [ForceDeleteAction::class, 'forceDelete', false, false, true],
        'force delete action with policy returning denied response' => fn (): array => [ForceDeleteAction::class, 'forceDelete', Response::deny(), false, true],
        'restore action with policy returning true' => fn (): array => [RestoreAction::class, 'restore', true, true, true],
        'restore action with policy returning allowed response' => fn (): array => [RestoreAction::class, 'restore', Response::allow(), true, true],
        'restore action with policy returning false' => fn (): array => [RestoreAction::class, 'restore', false, false, true],
        'restore action with policy returning denied response' => fn (): array => [RestoreAction::class, 'restore', Response::deny(), false, true],
        'replicate action with policy returning true' => fn (): array => [ReplicateAction::class, 'replicate', true, true, false],
        'replicate action with policy returning allowed response' => fn (): array => [ReplicateAction::class, 'replicate', Response::allow(), true, false],
        'replicate action with policy returning false' => fn (): array => [ReplicateAction::class, 'replicate', false, false, false],
        'replicate action with policy returning denied response' => fn (): array => [ReplicateAction::class, 'replicate', Response::deny(), false, false],
        'delete bulk action with policy returning true' => fn (): array => [DeleteBulkAction::class, 'deleteAny', true, true, false, true],
        'delete bulk action with policy returning allowed response' => fn (): array => [DeleteBulkAction::class, 'deleteAny', Response::allow(), true, false, true],
        'delete bulk action with policy returning false' => fn (): array => [DeleteBulkAction::class, 'deleteAny', false, false, false, true],
        'delete bulk action with policy returning denied response' => fn (): array => [DeleteBulkAction::class, 'deleteAny', Response::deny(), false, false, true],
        'force delete bulk action with policy returning true' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', true, true, true, true],
        'force delete bulk action with policy returning allowed response' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', Response::allow(), true, true, true],
        'force delete bulk action with policy returning false' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', false, false, true, true],
        'force delete bulk action with policy returning denied response' => fn (): array => [ForceDeleteBulkAction::class, 'forceDeleteAny', Response::deny(), false, true, true],
        'restore bulk action with policy returning true' => fn (): array => [RestoreBulkAction::class, 'restoreAny', true, true, true, true],
        'restore bulk action with policy returning allowed response' => fn (): array => [RestoreBulkAction::class, 'restoreAny', Response::allow(), true, true, true],
        'restore bulk action with policy returning false' => fn (): array => [RestoreBulkAction::class, 'restoreAny', false, false, true, true],
        'restore bulk action with policy returning denied response' => fn (): array => [RestoreBulkAction::class, 'restoreAny', Response::deny(), false, true, true]]);

    it('can force render relation manager after create another', function (): void {
        $ticket = Ticket::factory()
            ->create();

        CreateAction::configureUsing(function (CreateAction $action): void {
            $action->forceRenderAfterCreateAnother(fn (mixed $livewire): bool => $livewire instanceof RelationManager);
        });

        $action = TestAction::make(CreateAction::class)->table();

        livewire(DepartmentsRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->assertSuccessful()
            ->assertCountTableRecords(0)
            ->assertActionExists($action)
            ->mountAction($action, ['another' => true])
            ->fillForm([
                'name' => $name = Str::random(),
            ])
            ->callMountedAction()
            ->assertHasNoFormErrors()
            ->assertCountTableRecords(1)
            ->assertSeeText($name);

        assertDatabaseHas(Department::class, ['name' => $name]);
    });
});

describe('attaching records', function (): void {
    it('can attach a single record with table select', function (): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create();

        livewire(DepartmentsWithAttachTableSelectRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $department->getKey(),
            ])
            ->assertHasNoFormErrors();

        assertDatabaseHas('department_ticket', [
            'department_id' => $department->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    });

    it('can attach multiple records with table select', function (): void {
        $ticket = Ticket::factory()->create();
        $departments = Department::factory(3)->create();

        livewire(DepartmentsWithAttachTableSelectRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $departments->pluck('id')->toArray(),
            ])
            ->assertHasNoFormErrors();

        foreach ($departments as $department) {
            assertDatabaseHas('department_ticket', [
                'department_id' => $department->getKey(),
                'ticket_id' => $ticket->getKey(),
            ]);
        }
    });

    it('rejects out-of-scope `recordId` when `tableSelect()` is paired with `recordSelectOptionsQuery()`', function (): void {
        $ticket = Ticket::factory()->create();
        Department::factory()->create(['name' => 'Active Engineering']);
        $outOfScopeDepartment = Department::factory()->create(['name' => 'Inactive Department']);

        livewire(DepartmentsWithAttachTableSelectAndModifiedQueryRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $outOfScopeDepartment->getKey(),
            ]);

        assertDatabaseMissing('department_ticket', [
            'department_id' => $outOfScopeDepartment->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    });

    it('can attach records when some are already related', function (): void {
        $ticket = Ticket::factory()->create();
        $alreadyAttachedDepartment = Department::factory()->create();
        $newDepartment = Department::factory()->create();

        // First, attach a department to the ticket
        $ticket->departments()->attach($alreadyAttachedDepartment);

        // Verify initial state
        expect($ticket->departments()->count())->toBe(1);

        // Now attach only the new department (the UI would filter out already-attached ones)
        livewire(DepartmentsWithAttachTableSelectRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $newDepartment->getKey(),
            ])
            ->assertHasNoFormErrors();

        // Verify the new department was attached
        assertDatabaseHas('department_ticket', [
            'department_id' => $newDepartment->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);

        // Verify total count is now 2
        expect($ticket->departments()->count())->toBe(2);
    });
});

describe('record access edge cases', function (): void {
    it('can access record for action after record no longer matches `TrashedFilter` in `BelongsToMany` relation manager', function (): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create();
        $ticket->departments()->attach($department);

        $department->delete();

        livewire(DepartmentsRelationManager::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->filterTable('trashed', false)
            ->assertCanSeeTableRecords([$department])
            ->tap(fn () => $department->restore())
            ->callAction(TestAction::make(DeleteAction::class)->table($department));

        expect($department->fresh()->trashed())->toBeTrue();
    });

    it('can access record for action after record no longer matches tab query in `BelongsToMany` relation manager', function (): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create(['name' => 'Accounting']);
        $ticket->departments()->attach($department);

        livewire(DepartmentsRelationManagerWithTabs::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->set('activeTab', 'a_names')
            ->assertCanSeeTableRecords([$department])
            ->tap(fn () => $department->update(['name' => 'Billing']))
            ->callAction(TestAction::make(DeleteAction::class)->table($department));

        expect($department->fresh()->trashed())->toBeTrue();
    });

    it('cannot access record for action after record no longer matches tab without `excludeQueryWhenResolvingRecord()` in `BelongsToMany` relation manager', function (): void {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create(['name' => 'Accounting']);
        $ticket->departments()->attach($department);

        livewire(DepartmentsRelationManagerWithTabs::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->set('shouldExcludeTabQueryWhenResolvingRecord', false)
            ->set('activeTab', 'a_names')
            ->assertCanSeeTableRecords([$department])
            ->tap(fn () => $department->update(['name' => 'Billing']));

        livewire(DepartmentsRelationManagerWithTabs::class, [
            'ownerRecord' => $ticket,
            'pageClass' => EditTicket::class,
        ])
            ->set('shouldExcludeTabQueryWhenResolvingRecord', false)
            ->set('activeTab', 'a_names')
            ->mountTableAction(DeleteAction::class, $department)
            ->assertTableActionNotMounted(DeleteAction::class);
    });
});

// https://github.com/filamentphp/filament/issues/19594
it('can summarize pivot columns in a `BelongsToMany` `RelationManager`', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->create();

    // Attach departments with pivot data
    $ticket->departments()->attach($departments[0], ['quantity' => 10, 'price' => 1000]);
    $ticket->departments()->attach($departments[1], ['quantity' => 20, 'price' => 2000]);
    $ticket->departments()->attach($departments[2], ['quantity' => 30, 'price' => 3000]);

    // Test both implicit (`quantity`) and explicit (`pivot.price`) pivot column summarizers
    livewire(DepartmentsWithPivotSummaryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertSuccessful()
        ->assertTableColumnSummarySet('quantity', 'quantity_sum', 60)    // 10 + 20 + 30
        ->assertTableColumnSummarySet('pivot.price', 'price_sum', 6000); // 1000 + 2000 + 3000
});

it('preserves `modifyQueryUsing()` `addSelect()` subqueries when resolving a `BelongsToMany` record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();
    $ticket->departments()->attach($department);

    livewire(DepartmentsWithModifiedQueryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertTableColumnStateSet('virtual_label', 'preserved', $department->getKey());
});

it('still resolves pivot columns on a single `BelongsToMany` record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();
    $ticket->departments()->attach($department, ['quantity' => 42, 'price' => 1234]);

    livewire(DepartmentsWithPivotSummaryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertTableColumnStateSet('quantity', 42, $department->getKey())
        ->assertTableColumnStateSet('pivot.price', 1234, $department->getKey());
});

it('resolves pivot columns alongside `modifyQueryUsing()` virtual columns on a single `BelongsToMany` record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create(['name' => 'Engineering']);
    $ticket->departments()->attach($department, ['quantity' => 7, 'price' => 99]);

    livewire(DepartmentsWithPivotAndModifiedQueryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertTableColumnStateSet('name', 'Engineering', $department->getKey())
        ->assertTableColumnStateSet('quantity', 7, $department->getKey())
        ->assertTableColumnStateSet('pivot.price', 99, $department->getKey())
        ->assertTableColumnStateSet('virtual_label', 'preserved', $department->getKey());
});

it('resolves pivot columns and `modifyQueryUsing()` virtual columns across multiple `BelongsToMany` records', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(2)->create();
    $ticket->departments()->attach($departments[0], ['quantity' => 10, 'price' => 100]);
    $ticket->departments()->attach($departments[1], ['quantity' => 20, 'price' => 200]);

    livewire(DepartmentsWithPivotAndModifiedQueryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($departments)
        ->assertTableColumnStateSet('quantity', 10, $departments[0]->getKey())
        ->assertTableColumnStateSet('quantity', 20, $departments[1]->getKey())
        ->assertTableColumnStateSet('pivot.price', 100, $departments[0]->getKey())
        ->assertTableColumnStateSet('pivot.price', 200, $departments[1]->getKey())
        ->assertTableColumnStateSet('virtual_label', 'preserved', $departments[0]->getKey())
        ->assertTableColumnStateSet('virtual_label', 'preserved', $departments[1]->getKey());
});

it('can summarize both pivot and non-pivot columns in a `BelongsToMany` `RelationManager`', function (): void {
    $ticket = Ticket::factory()->create();
    $departments = Department::factory()->count(3)->create();

    $ticket->departments()->attach($departments[0], ['quantity' => 10, 'price' => 1000]);
    $ticket->departments()->attach($departments[1], ['quantity' => 20, 'price' => 2000]);
    $ticket->departments()->attach($departments[2], ['quantity' => 30, 'price' => 3000]);

    livewire(DepartmentsWithMixedSummaryRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
    ])
        ->assertSuccessful()
        ->assertTableColumnSummarySet('name', 'name_count', 3)
        ->assertTableColumnSummarySet('quantity', 'quantity_sum', 60)
        ->assertTableColumnSummarySet('pivot.price', 'price_sum', 6000);
});

it('defers the tab badge loading when `$isBadgeDeferred` is `true`', function (): void {
    $ticket = Ticket::factory()->create();

    $tab = DepartmentsWithDeferredBadgeRelationManager::getTabComponent($ticket, EditTicket::class);

    expect($tab->isBadgeDeferred())->toBeTrue();
});

it('resolves the deferred tab badge value from `getBadge()`', function (): void {
    $ticket = Ticket::factory()
        ->hasAttached(Department::factory(3))
        ->create();

    $tab = DepartmentsWithDeferredBadgeRelationManager::getTabComponent($ticket, EditTicket::class);

    expect($tab->isBadgeDeferred())->toBeTrue()
        ->and($tab->getBadge())->toBe('3');
});

it('does not defer the tab badge loading by default', function (): void {
    $ticket = Ticket::factory()->create();

    $tab = DepartmentsRelationManager::getTabComponent($ticket, EditTicket::class);

    expect($tab->isBadgeDeferred())->toBeFalse();
});
