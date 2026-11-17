<?php

use Filament\Facades\Filament;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Policies\DepartmentPolicy;
use Filament\Tests\Fixtures\Policies\TicketPolicy;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\ListTickets;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

afterEach(function (): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization(false);

    app()->bind(TicketPolicy::class . '::update', fn (): bool => true);
    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => true);
});

it('does not automatically authorize resource editable columns, even in strict mode', function (bool $isAuthorizationStrict): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization($isAuthorizationStrict);

    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    livewire(ListTicketsWithEditableColumns::class)
        ->assertTableColumnEnabled('is_open', $ticket)
        ->call('updateTableColumnState', 'is_open', (string) $ticket->getKey(), true)
        ->assertReturned(true);
})->with([false, true]);

it('does not automatically authorize relation manager editable columns, even in strict mode', function (bool $isAuthorizationStrict): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization($isAuthorizationStrict);

    $ticket = Ticket::factory()
        ->hasAttached(Department::factory(state: ['name' => 'Original']))
        ->create();

    $department = $ticket->departments->first();

    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => false);

    livewire(DepartmentsRelationManagerWithEditableColumns::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
        ->assertTableColumnEnabled('name', $department)
        ->call('updateTableColumnState', 'name', (string) $department->getKey(), 'Changed');

    expect($department->fresh()->name)->toBe('Changed');
})->with([false, true]);

it('can opt resource editable columns into authorization globally using `configureUsing()`', function (bool $isAuthorizationStrict): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization($isAuthorizationStrict);

    $ticket = Ticket::factory()->create();

    app()->bind(TicketPolicy::class . '::update', fn (): bool => false);

    ToggleColumn::configureUsing(
        fn (ToggleColumn $column) => $column->authorize(),
        during: function () use ($ticket): void {
            livewire(ListTicketsWithEditableColumns::class)
                ->assertTableColumnDisabled('is_open', $ticket)
                ->assertTableColumnEnabled('is_open_always_authorized', $ticket)
                ->call('updateTableColumnState', 'is_open', (string) $ticket->getKey(), true)
                ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);
        },
    );
})->with([false, true]);

it('can opt relation manager editable columns into authorization globally using `configureUsing()`', function (bool $isAuthorizationStrict): void {
    Filament::getCurrentOrDefaultPanel()->strictAuthorization($isAuthorizationStrict);

    $ticket = Ticket::factory()
        ->hasAttached(Department::factory(state: ['name' => 'Original']))
        ->create();

    $department = $ticket->departments->first();

    app()->bind(DepartmentPolicy::class . '::update', fn (): bool => false);

    TextInputColumn::configureUsing(
        fn (TextInputColumn $column) => $column->authorize(),
        during: function () use ($ticket, $department): void {
            livewire(DepartmentsRelationManagerWithEditableColumns::class, ['ownerRecord' => $ticket, 'pageClass' => EditTicket::class])
                ->assertTableColumnDisabled('name', $department)
                ->assertTableColumnEnabled('name_always_authorized', $department)
                ->call('updateTableColumnState', 'name', (string) $department->getKey(), 'Changed')
                ->assertReturned(['error' => __('filament-tables::table.columns.editable.unavailable')]);
        },
    );

    expect($department->fresh()->name)->toBe('Original');
})->with([false, true]);

it('checks enabled and disabled column assertions against the correct duplicate relationship row', function (): void {
    $ticket = Ticket::factory()->create();
    $department = Department::factory()->create();

    $ticket->departments()->attach($department, ['id' => 10, 'price' => 100]);
    $ticket->departments()->attach($department, ['id' => 20, 'price' => 0]);

    $livewire = livewire(DepartmentsRelationManagerWithEditableColumns::class, [
        'ownerRecord' => $ticket,
        'pageClass' => EditTicket::class,
        'shouldAllowDuplicates' => true,
    ]);

    $records = $livewire->instance()->getTableRecords();

    $livewire
        ->assertTableColumnEnabled('name', $records->firstWhere('pivot.price', 100))
        ->assertTableColumnDisabled('name', $records->firstWhere('pivot.price', 0));
});

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
    public bool $shouldAllowDuplicates = false;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->allowDuplicates($this->shouldAllowDuplicates)
            ->columns([
                TextInputColumn::make('name')
                    ->disabled(fn ($record): bool => $this->shouldAllowDuplicates && ($record->pivot->price === 0)),
                TextInputColumn::make('name_always_authorized')
                    ->getStateUsing(fn ($record): ?string => $record->name)
                    ->authorize(true),
            ]);
    }
}
