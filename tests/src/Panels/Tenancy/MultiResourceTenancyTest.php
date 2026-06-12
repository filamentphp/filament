<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Tenancy\NonTenantScopedUsers\NonTenantScopedUserResource;
use Filament\Tests\Fixtures\Resources\Tenancy\TenantScopedUsers\TenantScopedUserResource;
use Filament\Tests\Panels\Pages\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $panel = Filament::getPanel('tenancy');
    Filament::setCurrentPanel($panel);
});

it('can query a non-scoped resource when another resource registers a tenant scope on the same model', function (): void {
    $team = Team::factory()->create();

    $userInTenant = User::factory()->create();
    $userInTenant->teams()->attach($team);

    $userNotInTenant = User::factory()->create();

    $this->actingAs($userInTenant);
    Filament::setTenant($team);

    $panel = Filament::getCurrentOrDefaultPanel();
    TenantScopedUserResource::registerTenancyModelGlobalScope($panel);

    $results = NonTenantScopedUserResource::getEloquentQuery()->get();

    expect($results->pluck('id')->toArray())
        ->toContain($userInTenant->id)
        ->toContain($userNotInTenant->id);
});

it('can scope a resource to the current tenant', function (): void {
    $team = Team::factory()->create();

    $userInTenant = User::factory()->create();
    $userInTenant->teams()->attach($team);

    $userNotInTenant = User::factory()->create();

    $this->actingAs($userInTenant);
    Filament::setTenant($team);

    $panel = Filament::getCurrentOrDefaultPanel();
    TenantScopedUserResource::registerTenancyModelGlobalScope($panel);

    $results = TenantScopedUserResource::getEloquentQuery()->get();

    expect($results->pluck('id')->toArray())
        ->toContain($userInTenant->id)
        ->not->toContain($userNotInTenant->id);
});

it('can create a model when multiple resources observe tenancy model creation on the same model', function (): void {
    $adminUser = User::factory()->create();
    $team = Team::factory()->create();

    $this->actingAs($adminUser);
    Filament::setTenant($team);

    $panel = Filament::getCurrentOrDefaultPanel();
    TenantScopedUserResource::observeTenancyModelCreation($panel);
    TenantScopedUserResource::observeTenancyModelCreation($panel);

    $newUser = User::create([
        'name' => 'Test User',
        'email' => 'test-' . uniqid() . '@example.com',
        'password' => bcrypt('password'),
    ]);

    $pivotCount = $team->users()->where('user_id', $newUser->id)->count();

    expect($pivotCount)->toBe(1);
});
