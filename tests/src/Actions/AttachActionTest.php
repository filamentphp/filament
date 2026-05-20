<?php

use Filament\Actions\AttachAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithAttachActionRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithModifiedAttachQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithMultipleModifiedAttachQueryRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithPreloadedAttachRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithRecordSelectSearchColumnsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(TestCase::class);

describe('attaching records', function (): void {
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
});

describe('record select options', function (): void {
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

    it('rejects a `recordId` excluded by `recordSelectOptionsQuery()` when submitted directly', function (): void {
        $ticket = Ticket::factory()->create();
        Department::factory()->create(['name' => 'Active Engineering']);
        $outOfScopeDepartment = Department::factory()->create(['name' => 'Inactive Department']);

        livewire(DepartmentsWithModifiedAttachQueryRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => $outOfScopeDepartment->getKey(),
            ])
            ->assertHasActionErrors(['recordId']);

        assertDatabaseMissing('department_ticket', [
            'department_id' => $outOfScopeDepartment->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    });

    it('rejects a multi-attach batch containing an out-of-scope `recordId`', function (): void {
        $ticket = Ticket::factory()->create();
        $inScopeDepartment = Department::factory()->create(['name' => 'Active Engineering']);
        $outOfScopeDepartment = Department::factory()->create(['name' => 'Inactive Department']);

        livewire(DepartmentsWithMultipleModifiedAttachQueryRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->callAction(TestAction::make(AttachAction::class)->table(), [
                'recordId' => [$inScopeDepartment->getKey(), $outOfScopeDepartment->getKey()],
            ])
            ->assertHasActionErrors();

        assertDatabaseMissing('department_ticket', [
            'department_id' => $outOfScopeDepartment->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);

        assertDatabaseMissing('department_ticket', [
            'department_id' => $inScopeDepartment->getKey(),
            'ticket_id' => $ticket->getKey(),
        ]);
    });

    it('applies `recordSelectOptionsQuery()` to search results', function (): void {
        $ticket = Ticket::factory()->create();
        Department::factory()->create(['name' => 'Active Engineering']);
        Department::factory()->create(['name' => 'Inactive Engineering']);

        livewire(DepartmentsWithModifiedAttachQueryRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->mountAction(TestAction::make(AttachAction::class)->table())
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
                $results = $select->getSearchResults('Engineering');

                expect($results)->toHaveCount(1);
                expect(array_values($results))->toContain('Active Engineering');
                expect(array_values($results))->not->toContain('Inactive Engineering');

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
});

describe('option labels', function (): void {
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

    it('returns `null` from `getOptionLabel()` when `recordSelectOptionsQuery()` excludes the record', function (): void {
        $ticket = Ticket::factory()->create();
        $outOfScopeDepartment = Department::factory()->create(['name' => 'Inactive Department']);

        livewire(DepartmentsWithModifiedAttachQueryRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->mountAction(TestAction::make(AttachAction::class)->table())
            ->fillForm(['recordId' => $outOfScopeDepartment->getKey()])
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select): bool {
                expect($select->getOptionLabel(withDefault: false))->toBeNull();

                return true;
            });
    });

    it('omits out-of-scope records from `getOptionLabels()` when `recordSelectOptionsQuery()` excludes them', function (): void {
        $ticket = Ticket::factory()->create();
        $inScopeDepartment = Department::factory()->create(['name' => 'Active Engineering']);
        $outOfScopeDepartment = Department::factory()->create(['name' => 'Inactive Department']);

        livewire(DepartmentsWithMultipleModifiedAttachQueryRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->mountAction(TestAction::make(AttachAction::class)->table())
            ->fillForm(['recordId' => [$inScopeDepartment->getKey(), $outOfScopeDepartment->getKey()]])
            ->assertSchemaComponentExists('recordId', checkComponentUsing: function (Select $select) use ($inScopeDepartment, $outOfScopeDepartment): bool {
                $labels = $select->getOptionLabels(withDefaults: false);

                expect($labels)->toHaveCount(1);
                expect($labels)->toHaveKey($inScopeDepartment->getKey());
                expect($labels)->not->toHaveKey($outOfScopeDepartment->getKey());

                return true;
            });
    });
});

it('can set `attachAnother()`', function (): void {
    $action = AttachAction::make();

    expect($action->canAttachAnother())->toBeTrue();

    $action->attachAnother(false);

    expect($action->canAttachAnother())->toBeFalse();
});

it('can set `forceSearchCaseInsensitive()`', function (): void {
    $action = AttachAction::make();

    expect($action->isSearchForcedCaseInsensitive())->toBeNull();

    $action->forceSearchCaseInsensitive();

    expect($action->isSearchForcedCaseInsensitive())->toBeTrue();
});

it('can set `multiple()` on `AttachAction`', function (): void {
    $action = AttachAction::make();

    expect($action->isMultiple())->toBeFalse();

    $action->multiple();

    expect($action->isMultiple())->toBeTrue();
});

it('has `attach` as default name', function (): void {
    expect(AttachAction::getDefaultName())->toBe('attach');
});

it('can set `attachAnother()` with a `Closure`', function (): void {
    $action = AttachAction::make()
        ->attachAnother(static fn (): bool => false);

    expect($action->canAttachAnother())->toBeFalse();
});

it('can disable `attachAnother()` via deprecated `disableAttachAnother()`', function (): void {
    $action = AttachAction::make();

    expect($action->canAttachAnother())->toBeTrue();

    $action->disableAttachAnother();

    expect($action->canAttachAnother())->toBeFalse();
});

it('can set `preloadRecordSelect()`', function (): void {
    $action = AttachAction::make();

    expect($action->isRecordSelectPreloaded())->toBeFalse();

    $action->preloadRecordSelect();

    expect($action->isRecordSelectPreloaded())->toBeTrue();
});

it('can set `tableSelect()` configuration', function (): void {
    $action = AttachAction::make();

    expect($action->getTableSelectConfiguration())->toBeNull();

    $action->tableSelect('my-config');

    expect($action->getTableSelectConfiguration())->toBe('my-config');
});

it('can set `recordSelectSearchColumns()` with a `Closure`', function (): void {
    $action = AttachAction::make()
        ->recordSelectSearchColumns(static fn (): array => ['name', 'email']);

    expect($action->getRecordSelectSearchColumns())->toBe(['name', 'email']);
});

it('can clear `recordSelectSearchColumns()` with `null`', function (): void {
    $action = AttachAction::make()
        ->recordSelectSearchColumns(['name'])
        ->recordSelectSearchColumns(null);

    expect($action->getRecordSelectSearchColumns())->toBeNull();
});

it('returns fluent `$this` from `recordSelect()`', function (): void {
    $action = AttachAction::make();

    $result = $action->recordSelect(static fn ($select) => $select);

    expect($result)->toBe($action);
});

it('returns fluent `$this` from `recordSelectOptionsQuery()`', function (): void {
    $action = AttachAction::make();

    $result = $action->recordSelectOptionsQuery(static fn ($query) => $query);

    expect($result)->toBe($action);
});

it('can set `preloadRecordSelect()` with a `Closure`', function (): void {
    $action = AttachAction::make()
        ->preloadRecordSelect(static fn (): bool => true);

    expect($action->isRecordSelectPreloaded())->toBeTrue();
});

it('can set `multiple()` with a `Closure`', function (): void {
    $action = AttachAction::make()
        ->multiple(static fn (): bool => true);

    expect($action->isMultiple())->toBeTrue();
});

it('can set `forceSearchCaseInsensitive()` with a `Closure`', function (): void {
    $action = AttachAction::make()
        ->forceSearchCaseInsensitive(static fn (): bool => true);

    expect($action->isSearchForcedCaseInsensitive())->toBeTrue();
});

it('can set `tableSelect()`', function (): void {
    $action = AttachAction::make()
        ->tableSelect('App\\Tables\\TeamsTable');

    expect($action->getTableSelectConfiguration())->toBe('App\\Tables\\TeamsTable');
});

it('can set `tableSelect()` with a `Closure`', function (): void {
    $action = AttachAction::make()
        ->tableSelect(static fn (): string => 'App\\Tables\\TeamsTable');

    expect($action->getTableSelectConfiguration())->toBe('App\\Tables\\TeamsTable');
});

it('returns `null` for `getTableSelectConfiguration()` by default', function (): void {
    $action = AttachAction::make();

    expect($action->getTableSelectConfiguration())->toBeNull();
});
