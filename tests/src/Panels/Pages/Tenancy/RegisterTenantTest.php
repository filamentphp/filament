<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Pages\Tenancy\RegisterTeam;
use Filament\Tests\Panels\Pages\TestCase;
use Illuminate\Database\Eloquent\Builder;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('tenancy'));
});

it('preserves native restoration for a tenant registration page model', function (): void {
    $tenant = Team::factory()->create();
    $component = livewire(RegisterTeam::class, ['tenant' => $tenant])->assertSuccessful();
    $globalScopes = Team::getAllGlobalScopes();

    try {
        Team::addGlobalScope(
            'exclude-registered-tenant',
            static fn (Builder $query): Builder => $query->whereKeyNot($tenant->getKey()),
        );

        $component->call('$refresh')->assertSuccessful();

        expect($component->instance()->tenant->is($tenant))->toBeTrue();
    } finally {
        Team::setAllGlobalScopes($globalScopes);
    }
});

it('still checks `canView()` on subsequent tenant registration requests', function (): void {
    AuthorizableRegisterTeam::$isAllowed = true;
    $component = livewire(AuthorizableRegisterTeam::class)->assertSuccessful();

    try {
        AuthorizableRegisterTeam::$isAllowed = false;

        $component->call('$refresh')->assertNotFound();
    } finally {
        AuthorizableRegisterTeam::$isAllowed = true;
    }
});

class AuthorizableRegisterTeam extends RegisterTeam
{
    public static bool $isAllowed = true;

    public static function canView(): bool
    {
        return static::$isAllowed;
    }
}
