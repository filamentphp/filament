<?php

use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Livewire\Mechanisms\ComponentRegistry;

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

it('requires email verification before setting up required multi-factor authentication', function (): void {
    Filament::setCurrentPanel('slugs');

    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user);

    $setupUrl = Filament::getSetUpRequiredMultiFactorAuthenticationUrl();

    $this->get($setupUrl)
        ->assertRedirect(Filament::getEmailVerificationPromptUrl());

    $this->get(Filament::getVerifyEmailUrl($user))
        ->assertRedirect($setupUrl);

    expect($user->refresh()->hasVerifiedEmail())->toBeTrue();

    $this->get($setupUrl)
        ->assertSuccessful();
});

it('registers required authentication setup routes for each domain in the console', function (array $domains): void {
    $registry = app(PanelRegistry::class);
    $panels = $registry->panels;
    $environment = app()->environment();

    $registry->panels = [Panel::make()
        ->id('console-authentication')
        ->domains($domains)
        ->emailVerification()
        ->emailVerifiedMiddlewareName('custom-verified')
        ->multiFactorAuthentication([EmailAuthentication::make()], isRequired: true)];
    app()->instance('env', 'production');

    try {
        expect(app()->runningInConsole())->toBeTrue();

        require __DIR__ . '/../../../../../packages/panels/routes/web.php';

        Route::getRoutes()->refreshNameLookups();

        foreach ($domains ?: [null] as $domain) {
            $routeNamePrefix = 'filament.console-authentication.' . ((count($domains) > 1) ? "{$domain}." : '');
            $route = Route::getRoutes()->getByName("{$routeNamePrefix}auth.multi-factor-authentication.set-up-required");

            expect($route->gatherMiddleware())
                ->toContain("custom-verified:{$routeNamePrefix}auth.email-verification.prompt");
        }
    } finally {
        $registry->panels = $panels;
        app()->instance('env', $environment);
    }
})->with([
    'no domain' => [[]],
    'single domain' => [['admin.example.com']],
    'multiple domains' => [['admin.example.com', 'admin.example.org']],
]);

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
        ->first(fn (string $snapshot): bool => json_decode($snapshot, associative: true)['memo']['name'] === app(ComponentRegistry::class)->getName(EditProfile::class));

    $sendLivewireRequest = fn (): TestResponse => $this
        ->withHeaders(['X-Livewire' => 'true'])
        ->postJson(app('livewire')->getUpdateUri(), [
            'components' => [['snapshot' => $snapshot, 'updates' => [], 'calls' => []]],
        ]);

    $sendLivewireRequest()->assertSuccessful();

    $user->toggleEmailAuthentication(false);

    $sendLivewireRequest()->assertRedirect(Filament::getSetUpRequiredMultiFactorAuthenticationUrl());
});
