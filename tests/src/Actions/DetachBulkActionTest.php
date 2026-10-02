<?php

use Filament\Actions\DetachBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\DepartmentTicket;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsWithDetachBulkActionRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;

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

it('returns `detach` from `getDefaultName()`', function (): void {
    expect(DetachBulkAction::getDefaultName())->toBe('detach');
});

it('counts pivot event vetoes and authorization failures separately when detaching', function (bool $hasPivotPrimaryKey, bool $throwsException): void {
    $ticket = Ticket::factory()->create();
    $otherTicket = Ticket::factory()->create();
    [$allowedDepartment, $vetoedDepartment, $unauthorizedDepartment, $unselectedDepartment] = Department::factory()->count(4)->hasAttached($ticket)->create()->all();
    $otherTicket->departments()->attach($allowedDepartment);
    $attemptedRecords = [];
    $counts = collect();
    $transactionLevel = DB::transactionLevel();
    Exceptions::fake();

    DepartmentTicket::deleting(static function (DepartmentTicket $pivot) use ($vetoedDepartment, &$attemptedRecords, $throwsException): bool {
        $attemptedRecords[] = $pivot->department_id;

        if ($throwsException && ($pivot->department_id === $vetoedDepartment->getKey())) {
            throw new RuntimeException('Detachment prevented');
        }

        return $pivot->department_id !== $vetoedDepartment->getKey();
    });

    DetachBulkAction::configureUsing(
        static fn (DetachBulkAction $action) => $action
            ->databaseTransaction()
            ->before(static fn (Table $table) => $table->relationship(static fn (): BelongsToMany => $ticket->departments()->using(DepartmentTicket::class)->withPivot($hasPivotPrimaryKey ? ['id'] : [])))
            ->authorizeIndividualRecords(static fn (Department $record): bool => ! $record->is($unauthorizedDepartment))
            ->failureNotificationTitle(static function (int $successCount, int $failureCount, int $totalCount, int $missingProcessingFailureMessageCount, array $processingFailureMessages) use ($counts): string {
                $counts->push($successCount, $failureCount, $totalCount, $missingProcessingFailureMessageCount, $processingFailureMessages);

                return 'Some departments could not be detached';
            }),
        during: static fn () => livewire(DepartmentsWithDetachBulkActionRelationManager::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
            ->callTableBulkAction(DetachBulkAction::class, [$allowedDepartment, $vetoedDepartment, $unauthorizedDepartment])
            ->assertDispatched('deselectAllTableRecords'),
    );

    expect($ticket->departments()->pluck('departments.id')->all())->toEqualCanonicalizing([$vetoedDepartment->getKey(), $unauthorizedDepartment->getKey(), $unselectedDepartment->getKey()])
        ->and($otherTicket->departments()->pluck('departments.id')->all())->toBe([$allowedDepartment->getKey()])
        ->and(Department::count())->toBe(4)
        ->and($attemptedRecords)->toEqualCanonicalizing([$allowedDepartment->getKey(), $vetoedDepartment->getKey()])
        ->and($counts->all())->toBe([1, 2, 3, 1, []])
        ->and(DB::transactionLevel())->toBe($transactionLevel);

    Exceptions::assertReportedCount($throwsException ? 1 : 0);
})->with([false, true])->with([false, true]);
