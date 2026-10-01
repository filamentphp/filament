<?php

use Filament\Actions\DetachAction;
use Filament\Actions\Enums\ActionStatus;
use Filament\Actions\Testing\TestAction;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\DepartmentTicket;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithDetachActionRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseMissing;

uses(TestCase::class);

it('can render `DetachAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertActionExists(TestAction::make(DetachAction::class)->table($department));
});

it('can mount `DetachAction` confirmation modal', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(DetachAction::class)->table($department))
        ->assertActionMounted(TestAction::make(DetachAction::class)->table($department));
});

it('can detach a record using `DetachAction`', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(DetachAction::class)->table($department));

    assertDatabaseMissing('department_ticket', [
        'department_id' => $department->getKey(),
        'ticket_id' => $ticket->getKey(),
    ]);
});

it('does not delete the record when detaching', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(DetachAction::class)->table($department));

    expect(Department::find($department->getKey()))->not->toBeNull();
});

it('can show success notification after detaching a record', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(DetachAction::class)->table($department))
        ->assertNotified();
});

it('removes record from table after detaching', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertCanSeeTableRecords([$department])
        ->callAction(TestAction::make(DetachAction::class)->table($department))
        ->assertCanNotSeeTableRecords([$department]);
});

it('can detach multiple records sequentially', function (): void {
    $ticket = Ticket::factory()->create();
    $department1 = Department::factory()->hasAttached($ticket)->create();
    $department2 = Department::factory()->hasAttached($ticket)->create();

    livewire(DepartmentsWithDetachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->callAction(TestAction::make(DetachAction::class)->table($department1))
        ->callAction(TestAction::make(DetachAction::class)->table($department2));

    assertDatabaseMissing('department_ticket', [
        'department_id' => $department1->getKey(),
        'ticket_id' => $ticket->getKey(),
    ]);

    assertDatabaseMissing('department_ticket', [
        'department_id' => $department2->getKey(),
        'ticket_id' => $ticket->getKey(),
    ]);
});

it('returns `detach` from `getDefaultName()`', function (): void {
    expect(DetachAction::getDefaultName())->toBe('detach');
});

it('fails when a custom pivot event vetoes detachment', function (bool $allowsDuplicates): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();
    $statuses = collect();

    DepartmentTicket::deleting(static fn (): bool => false);

    DetachAction::configureUsing(
        static fn (DetachAction $action) => $action
            ->before(static function (Table $table, Department $record) use ($ticket, $allowsDuplicates): void {
                $relationship = $ticket->departments()->using(DepartmentTicket::class)->withPivot('id');
                $table->relationship(static fn (): BelongsToMany => $relationship)->allowDuplicates($allowsDuplicates);
                $record->setRelation('pivot', $relationship->find($record->getKey())->pivot);
            })
            ->after(static function (DetachAction $action) use ($statuses): void {
                $statuses->push($action->getStatus());
            }),
        during: static fn () => livewire(DepartmentsWithDetachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->callAction(TestAction::make(DetachAction::class)->table($department))
            ->assertNotNotified(),
    );

    expect($ticket->departments()->pluck('departments.id')->all())->toBe([$department->getKey()])
        ->and($statuses->all())->toBe([ActionStatus::Failure]);
})->with([false, true]);

it('preserves a custom `using()` callback without a return value when detaching', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->hasAttached($ticket)->create();

    DetachAction::configureUsing(
        static fn (DetachAction $action) => $action->using(static function (Department $record) use ($ticket): void {
            $ticket->departments()->detach($record);
        }),
        during: static fn () => livewire(DepartmentsWithDetachActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->callAction(TestAction::make(DetachAction::class)->table($department))
            ->assertNotified(),
    );

    expect($ticket->departments()->count())->toBe(0);
});
