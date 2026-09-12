<?php

use Filament\Resources\ParentResourceRegistration;
use Filament\Tests\Fixtures\Models\Company;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Companies\Resources\CompanyTeamResource;
use Filament\Tests\Fixtures\Resources\Tickets\Resources\TicketDepartmentResource;
use Filament\Tests\Fixtures\Resources\Users\Resources\ManagedUserPostResource;
use Filament\Tests\Fixtures\Resources\Users\UserResource;
use Filament\Tests\Panels\Resources\TestCase;

uses(TestCase::class);

it('resolves the parent relation page when the page key matches the relationship name', function (): void {
    $registration = TicketDepartmentResource::getParentResourceRegistration();

    expect($registration)
        ->toBeInstanceOf(ParentResourceRegistration::class)
        ->and($registration->resolveRelationshipPageName())->toBe('departments')
        ->and($registration->getRouteName())->toBe('departments');

    $parentRecord = Ticket::factory()->create();

    expect(TicketDepartmentResource::getIndexUrl([
        'ticket' => $parentRecord,
    ]))->toContain('/tickets/' . $parentRecord->getRouteKey() . '/departments');
});

it('discovers a manage related records page registered under a custom page key', function (): void {
    $registration = ManagedUserPostResource::getParentResourceRegistration();

    expect($registration->resolveRelationshipPageName())->toBe('managePosts')
        ->and($registration->getRouteName())->toBe('posts');

    $parentRecord = User::factory()->create();

    expect(ManagedUserPostResource::getIndexUrl([
        'author' => $parentRecord,
    ]))->toContain('/users/' . $parentRecord->getRouteKey() . '/manage-posts');
});

it('prefers an explicit page name over discovery', function (): void {
    $registration = UserResource::asParent(ManagedUserPostResource::class)
        ->relationship('posts')
        ->inverseRelationship('author')
        ->page('edit');

    expect($registration->getPageName())->toBe('edit')
        ->and($registration->resolveRelationshipPageName())->toBe('edit');
});

it('falls back to the parent view page when no relation page exists', function (): void {
    $parentRecord = Company::factory()->create();

    expect(CompanyTeamResource::getIndexUrl([
        'company' => $parentRecord,
    ]))->toContain('/companies/' . $parentRecord->getRouteKey())
        ->not->toContain('/teams');
});
