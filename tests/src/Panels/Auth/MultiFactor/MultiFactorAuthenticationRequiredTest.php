<?php

use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Livewire\Livewire;

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
        ->hasEmailAuthentication()
        ->create();

    $this->actingAs($user);

    $this->get(Filament::getSetUpRequiredMultiFactorAuthenticationUrl())
        ->assertRedirect(Filament::getProfileUrl());
});

it('redirects Livewire requests to the setup page once the user has disabled multi-factor authentication', function (): void {
    Filament::setCurrentPanel('required-multi-factor-authentication');

    $user = User::factory()
        ->hasEmailAuthentication()
        ->create();

    $this->actingAs($user);

    $html = $this->get(Filament::getProfileUrl())
        ->assertSuccessful()
        ->getContent();

    $snapshot = collect(Str::matchAll('/wire:snapshot="([^"]+)"/', $html))
        ->map(fn (string $snapshot): string => htmlspecialchars_decode($snapshot, ENT_QUOTES))
        ->first(fn (string $snapshot): bool => json_decode($snapshot, associative: true)['memo']['name'] === Livewire::new(EditProfile::class)->getName());

    $sendLivewireRequest = fn (): TestResponse => $this
        ->withHeaders(['X-Livewire' => 'true'])
        ->postJson(app('livewire')->getUpdateUri(), [
            'components' => [['snapshot' => $snapshot, 'updates' => [], 'calls' => []]],
        ]);

    $sendLivewireRequest()->assertSuccessful();

    $user->toggleEmailAuthentication(false);
    Livewire::flushState();

    $sendLivewireRequest()->assertRedirect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl());
});
