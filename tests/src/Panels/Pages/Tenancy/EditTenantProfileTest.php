<?php

use Filament\Facades\Filament;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Support\Facades\Gate;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('allows the user access to the tenant profile page if the user is authorized', function (): void {
    Filament::setTenant(Team::factory()->create());

    Gate::policy(Team::class, TeamPolicyWithAccess::class);

    livewire(EditTeamProfile::class)
        ->assertSuccessful();
});

it('denies the user access to the tenant profile page if the user is unauthorized', function (): void {
    Filament::setTenant(Team::factory()->create());

    Gate::policy(Team::class, TeamPolicyWithoutAccess::class);

    livewire(EditTeamProfile::class)
        ->assertNotFound();
});

it('re-authorizes the tenant profile page on Livewire updates after the initial mount', function (): void {
    Filament::setTenant(Team::factory()->create());

    Gate::policy(Team::class, TeamPolicyWithAccess::class);

    $component = livewire(EditTeamProfile::class);

    Gate::policy(Team::class, TeamPolicyWithoutAccess::class);

    $component
        ->set('data.name', 'foo')
        ->assertStatus(404);
});

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Edit team';
    }
}

class TeamPolicyWithAccess
{
    public function update(User $user, Team $team): bool
    {
        return true;
    }
}

class TeamPolicyWithoutAccess
{
    public function update(User $user, Team $team): bool
    {
        return false;
    }
}
