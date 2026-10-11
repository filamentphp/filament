<?php

namespace Filament\Tests\Panels\Pages\Tenancy;

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

it('restores the current tenant on Livewire updates', function (): void {
    $tenant = Team::factory()->create();
    Filament::setTenant(Team::query()->findOrFail($tenant->getKey()));
    Gate::policy(Team::class, TeamPolicyWithAccess::class);

    livewire(EditTeamProfile::class)
        ->set('data.name', 'Updated team')
        ->assertSuccessful();
});

it('preserves `hydrate()` overrides that call `parent::hydrate()`', function (): void {
    Filament::setTenant(Team::factory()->create());
    Gate::policy(Team::class, TeamPolicyWithAccess::class);

    livewire(EditTeamProfileWithHydrateHook::class)
        ->set('data.name', 'Updated team')
        ->assertSet('hasHydrated', true)
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

it('uses the current tenant when the tenant changes between requests', function (): void {
    $originalTenant = Team::factory()->create();
    $currentTenant = Team::factory()->create();
    Filament::setTenant($originalTenant);
    Gate::policy(Team::class, TeamPolicyWithAccess::class);
    $component = livewire(EditTeamProfile::class)
        ->assertSuccessful();

    Filament::setTenant($currentTenant);

    $component
        ->set('data.name', 'Updated current tenant')
        ->assertSuccessful();

    expect($component->instance()->tenant->is($currentTenant))->toBeTrue();
});

it('uses the current tenant before `boot()` on Livewire updates', function (): void {
    $originalTenant = Team::factory()->create();
    $currentTenant = Team::factory()->create();
    Filament::setTenant($originalTenant);
    Gate::policy(Team::class, TeamPolicyWithAccess::class);
    $component = livewire(EditTeamProfileWithBootHook::class)
        ->assertSuccessful();

    EditTeamProfileWithBootHook::$bootedTenantKey = null;
    Filament::setTenant($currentTenant);

    $component
        ->set('data.name', 'Updated current tenant')
        ->assertSuccessful();

    expect(EditTeamProfileWithBootHook::$bootedTenantKey)->toBe($currentTenant->getKey());
});

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Edit team';
    }
}

class EditTeamProfileWithHydrateHook extends EditTeamProfile
{
    public bool $hasHydrated = false;

    public function hydrate(): void
    {
        parent::hydrate();

        $this->hasHydrated = true;
    }
}

class EditTeamProfileWithBootHook extends EditTeamProfile
{
    public static int | string | null $bootedTenantKey = null;

    public function boot(): void
    {
        static::$bootedTenantKey = $this->tenant?->getKey();
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
