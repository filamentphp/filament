<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('redirects the user to the setup page if they have not set up multi-factor authentication', function (): void {
    Filament::setCurrentPanel('required-multi-factor-authentication');

    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(Filament::getUrl())
        ->assertRedirect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl());
});

it('can render setup page', function (): void {
    Filament::setCurrentPanel('required-multi-factor-authentication');

    $user = User::factory()->create();

    $this->actingAs($user);

    expect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl())->toEndWith('/multi-factor-authentication/set-up');

    $this->get(Filament::getSetUpRequiredMultiFactorAuthenticationUrl())
        ->assertSuccessful();
});

it('can render setup page with a custom slug', function (): void {
    Filament::setCurrentPanel('slugs');

    expect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl())->toEndWith('/multi-factor-authentication-test/set-up-test');

    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(Filament::getSetUpRequiredMultiFactorAuthenticationUrl())
        ->assertSuccessful();
});

it('redirects the user away from the setup page if they have already set up multi-factor authentication', function (): void {
    Filament::setCurrentPanel('required-multi-factor-authentication');

    $user = User::factory()
        ->hasEmailCodeAuthentication()
        ->create();

    $this->actingAs($user);

    $this->get(Filament::getSetUpRequiredMultiFactorAuthenticationUrl())
        ->assertRedirect(Filament::getProfileUrl());
});
