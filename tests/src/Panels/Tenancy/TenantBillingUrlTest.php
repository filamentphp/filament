<?php

use Filament\Facades\Filament;
use Filament\Livewire\Topbar;
use Filament\Panel;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;

uses(TestCase::class);

it('returns `null` for `getTenantBillingUrl()` when no tenant is set', function (): void {
    $panel = Panel::make()
        ->id('test')
        ->default()
        ->tenant(Team::class);

    Filament::setCurrentPanel($panel);
    Filament::setTenant(null);

    expect(Filament::getTenantBillingUrl())->toBeNull();
});

it('can resolve tenant menu items without error when no tenant is set', function (): void {
    $panel = Panel::make()
        ->id('test')
        ->default()
        ->tenant(Team::class);

    Filament::setCurrentPanel($panel);
    Filament::setTenant(null);

    expect(Filament::getTenantMenuItems())->toBeArray();
});

it('renders `Topbar` without error when panel has tenancy but no tenant is set', function (): void {
    actingAs(User::factory()->create());

    Filament::setCurrentPanel('tenant-menu-flat');
    Filament::setTenant(null);

    livewire(Topbar::class)
        ->assertSuccessful();
});

