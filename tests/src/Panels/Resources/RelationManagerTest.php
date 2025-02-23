<?php

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Actions\ViewAction;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Policies\DepartmentPolicy;
use Filament\Tests\Fixtures\Resources\Tickets\Pages\EditTicket;
use Filament\Tests\Fixtures\Resources\Tickets\RelationManagers\DepartmentsRelationManager;
use Filament\Tests\Panels\Resources\TestCase;
use Illuminate\Auth\Access\Response;

use function Filament\Tests\livewire;

uses(TestCase::class);

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
