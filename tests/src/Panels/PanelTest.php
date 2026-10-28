<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Panels\Pages\TestCase;

uses(TestCase::class);

it('can call `bootTenancy()` repeatedly while preserving associations for multiple resources of the same model', function (): void {
    $panel = Filament::getPanel('tenancy');
    Filament::setCurrentPanel($panel);
    $tenant = Team::factory()->create();
    Filament::setTenant($tenant);

    $panel->bootTenancy();
    $panel->bootTenancy();

    $user = User::factory()->create();

    expect($tenant->users()->where('user_id', $user->getKey())->count())->toBe(1)
        ->and($user->team_id)->toBe($tenant->getKey());
});
