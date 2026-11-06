<?php

use Filament\Facades\Filament;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\TicketMessage;
use Filament\Tests\Fixtures\Policies\DepartmentPolicy;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\TicketMessages\Pages\ListTicketMessages;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\ListTickets;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;

use function Filament\Tests\livewire;

uses(TestCase::class);

afterEach(function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization(false);

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => true);
});

it('does not check the resource `update()` policy for editable columns when authorization is not strict', function (bool | Response $policyResult): void {
    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool | Response => $policyResult);

    livewire(ListTicketsWithEditableColumns::class)
        ->assertTableColumnStateCanBeUpdated('is_open', $ticket);
})->with([
    'false' => [false],
    'denied response' => [Response::deny()],
]);

it('checks the resource `update()` policy for editable columns when authorization is strict', function (bool | Response $policyResult, bool $canUpdate): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool | Response => $policyResult);

    livewire(ListTicketsWithEditableColumns::class)
        ->{$canUpdate ? 'assertTableColumnStateCanBeUpdated' : 'assertTableColumnStateCanNotBeUpdated'}('is_open', $ticket);
})->with([
    'true' => [true, true],
    'allowed response' => [Response::allow(), true],
    'false' => [false, false],
    'denied response' => [Response::deny(), false],
]);

it('rejects editable column updates on the server when the resource `update()` policy denies access in strict mode', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    livewire(ListTicketsWithEditableColumns::class)
        ->call('updateTableColumnState', 'is_open', (string) $ticket->getKey(), true)
        ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);
});

it('prefers `authorize()` over the resource `update()` policy in strict mode', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    livewire(ListTicketsWithEditableColumns::class)
        ->assertTableColumnStateCanNotBeUpdated('is_open', $ticket)
        ->assertTableColumnStateCanBeUpdated('is_open_always_authorized', $ticket);
});

it('does not check the related resource `update()` policy for relation manager editable columns when authorization is not strict', function (): void {
    $ticket = Ticket::factory()
        ->hasAttached(Department::factory())
        ->create();

    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => false);

    livewire(DepartmentsRelationManagerWithEditableColumns::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertTableColumnStateCanBeUpdated('name', $ticket->departments->first());
});

it('checks the related `update()` policy for relation manager editable columns when authorization is strict', function (bool | Response $policyResult, bool $canUpdate): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()
        ->hasAttached(Department::factory())
        ->create();

    app()->bind(DepartmentPolicy::class . '::update', fn (): bool | Response => $policyResult);

    livewire(DepartmentsRelationManagerWithEditableColumns::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->{$canUpdate ? 'assertTableColumnStateCanBeUpdated' : 'assertTableColumnStateCanNotBeUpdated'}('name', $ticket->departments->first());
})->with([
    'true' => [true, true],
    'allowed response' => [Response::allow(), true],
    'false' => [false, false],
    'denied response' => [Response::deny(), false],
]);

it('rejects relation manager editable column updates on the server when the related `update()` policy denies access in strict mode', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()
        ->hasAttached(Department::factory(state: ['name' => 'Original']))
        ->create();

    $department = $ticket->departments->first();

    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => false);

    livewire(DepartmentsRelationManagerWithEditableColumns::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->call('updateTableColumnState', 'name', (string) $department->getKey(), 'Changed')
        ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);

    expect($department->fresh()->name)->toBe('Original');
});

it('saves relation manager editable column updates on the server when the related `update()` policy allows access in strict mode', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()
        ->hasAttached(Department::factory(state: ['name' => 'Original']))
        ->create();

    $department = $ticket->departments->first();

    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => true);

    livewire(DepartmentsRelationManagerWithEditableColumns::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->call('updateTableColumnState', 'name', (string) $department->getKey(), 'Changed');

    expect($department->fresh()->name)->toBe('Changed');
});

it('prefers `authorize()` over the related `update()` policy for relation manager editable columns in strict mode', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()
        ->hasAttached(Department::factory())
        ->create();

    $department = $ticket->departments->first();

    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => false);

    livewire(DepartmentsRelationManagerWithEditableColumns::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertTableColumnStateCanNotBeUpdated('name', $department)
        ->assertTableColumnStateCanBeUpdated('name_always_authorized', $department);
});

it('returns no default column authorization response from resource tables when authorization is not strict', function (): void {
    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    $livewire = livewire(ListTicketsWithEditableColumns::class)->instance();

    $column = $livewire->getTable()->getColumn('is_open')->record($ticket);

    expect($livewire->getDefaultTableColumnAuthorizationResponse($column))->toBeNull();
});

it('returns no default column authorization response from resource tables without a record in strict mode', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    $livewire = livewire(ListTicketsWithEditableColumns::class)->instance();

    $column = $livewire->getTable()->getColumn('is_open');

    expect($livewire->getDefaultTableColumnAuthorizationResponse($column))->toBeNull()
        ->and($column->isDisabled())->toBeFalse();
});

it('allows editable column updates on the server when the resource `update()` policy allows access in strict mode', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);

    livewire(ListTicketsWithEditableColumns::class)
        ->call('updateTableColumnState', 'is_open', (string) $ticket->getKey(), true)
        ->assertReturned(true);
});

it('returns the resource `update()` policy response as the default column authorization response in strict mode', function (bool | Response $policyResult, bool $isAllowed): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool | Response => $policyResult);

    $livewire = livewire(ListTicketsWithEditableColumns::class)->instance();

    $column = $livewire->getTable()->getColumn('is_open')->record($ticket);

    $response = $livewire->getDefaultTableColumnAuthorizationResponse($column);

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->allowed())->toBe($isAllowed)
        ->and($column->isAuthorized())->toBe($isAllowed)
        ->and($column->isDisabled())->toBe(! $isAllowed);
})->with([
    'true' => [true, true],
    'false' => [false, false],
    'denied response' => [Response::deny(), false],
]);

it('keeps the denial message from the resource `update()` policy in strict mode', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): Response => Response::deny('Ticket is locked.'));

    $column = livewire(ListTicketsWithEditableColumns::class)
        ->instance()
        ->getTable()
        ->getColumn('is_open')
        ->record($ticket);

    expect($column->getAuthorizationResponse()->message())->toBe('Ticket is locked.');
});

it('throws an exception for editable columns in strict mode when the resource has no policy', function (): void {
    $message = TicketMessage::factory()->create();

    $livewire = livewire(ListTicketMessages::class)->instance();

    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $column = ToggleColumn::make('is_open')->record($message);

    $livewire->getDefaultTableColumnAuthorizationResponse($column);
})->throws(LogicException::class);

it('does not throw an exception for editable columns without a policy when authorization is not strict', function (): void {
    $message = TicketMessage::factory()->create();

    $livewire = livewire(ListTicketMessages::class)->instance();

    $column = ToggleColumn::make('is_open')->record($message);

    expect($livewire->getDefaultTableColumnAuthorizationResponse($column))->toBeNull();
});

it('returns no default column authorization response from relation managers when authorization is not strict', function (): void {
    $ticket = Ticket::factory()
        ->hasAttached(Department::factory())
        ->create();

    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => false);

    $livewire = livewire(DepartmentsRelationManagerWithEditableColumns::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])->instance();

    $column = $livewire->getTable()->getColumn('name')->record($ticket->departments->first());

    expect($livewire->getDefaultTableColumnAuthorizationResponse($column))->toBeNull();
});

it('returns no default column authorization response from relation managers without a record in strict mode', function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization();

    $ticket = Ticket::factory()->create();

    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => false);

    $livewire = livewire(DepartmentsRelationManagerWithEditableColumns::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])->instance();

    $column = $livewire->getTable()->getColumn('name');

    expect($livewire->getDefaultTableColumnAuthorizationResponse($column))->toBeNull()
        ->and($column->isDisabled())->toBeFalse();
});

it('does not expose the default column authorization response method to the frontend', function (string $component, bool $isRelationManager): void {
    $ticket = Ticket::factory()->create();

    livewire($component, $isRelationManager ? ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class] : [])
        ->call('getDefaultTableColumnAuthorizationResponse')
        ->assertForbidden();
})->with([
    'list records page' => [ListTicketsWithEditableColumns::class, false],
    'relation manager' => [DepartmentsRelationManagerWithEditableColumns::class, true],
]);

class ListTicketsWithEditableColumns extends ListTickets
{
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ToggleColumn::make('is_open')
                    ->getStateUsing(fn (): bool => false)
                    ->updateStateUsing(fn (): bool => true),
                ToggleColumn::make('is_open_always_authorized')
                    ->getStateUsing(fn (): bool => false)
                    ->updateStateUsing(fn (): bool => true)
                    ->authorize(true),
            ]);
    }
}

class DepartmentsRelationManagerWithEditableColumns extends DepartmentsRelationManager
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextInputColumn::make('name'),
                TextInputColumn::make('name_always_authorized')
                    ->getStateUsing(fn ($record): ?string => $record->name)
                    ->authorize(true),
            ]);
    }
}
