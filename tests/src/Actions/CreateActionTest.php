<?php

use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManagerWithPreservation;
use Filament\Tests\Fixtures\Resources\Users\Pages\EditUser;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithCreateAndPreserveRepeaterRelationManager;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\PostsWithCreateAndPreserveRepeaterWithDefaultRelationManager;
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

it('can create another record and preserve data using `CreateAction`', function (): void {
    $ticket = Ticket::factory()->create();

    livewire(DepartmentsRelationManagerWithPreservation::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->mountAction(TestAction::make(CreateAction::class)->table(), ['another' => true])
        ->fillForm([
            'name' => $firstName = Str::random(),
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors()
        ->assertSchemaStateSet([
            'name' => $firstName,
        ])
        ->fillForm([
            'name' => $secondName = Str::random(),
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    assertDatabaseHas(Department::class, ['name' => $firstName]);
    assertDatabaseHas(Department::class, ['name' => $secondName]);
});

it('can create another record and preserve repeater data using `CreateAction`', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $repeaterItems = [
        ['name' => 'First Item', 'email' => 'first@example.com'],
        ['name' => 'Second Item', 'email' => 'second@example.com'],
    ];

    livewire(PostsWithCreateAndPreserveRepeaterRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(CreateAction::class)->table(), ['another' => true])
        ->fillForm([
            'title' => $firstTitle = Str::random(),
            'rating' => 5,
            'json_array_of_objects' => $repeaterItems,
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors()
        ->fillForm([
            'title' => $secondTitle = Str::random(),
            'rating' => 3,
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $record = Post::query()->where('title', $firstTitle)->first();

    expect($record)->not->toBeNull();
    expect($record->json_array_of_objects)->toBe($repeaterItems);

    $record2 = Post::query()->where('title', $secondTitle)->first();

    expect($record2)->not->toBeNull();
    expect($record2->json_array_of_objects)->toBe($repeaterItems);

    $undoRepeaterFake();
});

it('can create another record and preserve repeater data with `default()` values using `CreateAction`', function (): void {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $repeaterItems = [
        ['name' => 'Custom A', 'email' => 'a@example.com'],
        ['name' => 'Custom B', 'email' => 'b@example.com'],
        ['name' => 'Custom C', 'email' => 'c@example.com'],
    ];

    livewire(PostsWithCreateAndPreserveRepeaterWithDefaultRelationManager::class, ['ownerRecord' => $user, 'pageClass' => EditUser::class])
        ->mountAction(TestAction::make(CreateAction::class)->table(), ['another' => true])
        ->fillForm([
            'title' => $firstTitle = Str::random(),
            'rating' => 5,
            'json_array_of_objects' => $repeaterItems,
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors()
        ->fillForm([
            'title' => $secondTitle = Str::random(),
            'rating' => 3,
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    $record = Post::query()->where('title', $firstTitle)->first();

    expect($record)->not->toBeNull();
    expect($record->json_array_of_objects)->toBe($repeaterItems);

    $record2 = Post::query()->where('title', $secondTitle)->first();

    expect($record2)->not->toBeNull();
    expect($record2->json_array_of_objects)->toBe($repeaterItems);

    $undoRepeaterFake();
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
