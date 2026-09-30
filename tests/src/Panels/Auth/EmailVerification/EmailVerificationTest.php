<?php

use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Cache\RateLimiter;

uses(TestCase::class);

it('can verify an email', function (): void {
    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    expect($userToVerify)
        ->hasVerifiedEmail()->toBeFalse();

    $this
        ->actingAs($userToVerify)
        ->get(Filament::getVerifyEmailUrl($userToVerify))
        ->assertRedirect(Filament::getUrl());

    expect($userToVerify->refresh())
        ->hasVerifiedEmail()->toBeTrue();
});

it('can verify an email with a custom slug', function (): void {
    Filament::setCurrentPanel('slugs');

    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    expect($userToVerify)
        ->hasVerifiedEmail()->toBeFalse()
        ->and(Filament::getVerifyEmailUrl($userToVerify))->toContain('/email-verification-test/verify-test/');

    $this
        ->actingAs($userToVerify)
        ->get(Filament::getVerifyEmailUrl($userToVerify))
        ->assertRedirect(Filament::getUrl());

    expect($userToVerify->refresh())
        ->hasVerifiedEmail()->toBeTrue();
});

it('cannot verify an email when signed in as another user', function (): void {
    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $anotherUser = User::factory()->create([
        'email_verified_at' => null,
    ]);

    expect($anotherUser)
        ->hasVerifiedEmail()->toBeFalse();

    $this
        ->actingAs($anotherUser)
        ->get(Filament::getVerifyEmailUrl($userToVerify))
        ->assertForbidden();

    expect($anotherUser->refresh())
        ->hasVerifiedEmail()->toBeFalse();
});

it('scopes authentication route rate limits by authentication guard', function (): void {
    $user = User::factory()->create();
    $rateLimiter = app(RateLimiter::class)->limiter('filament-authentication');

    $this->actingAs($user);

    $firstLimit = $rateLimiter(request());

    config()->set('auth.guards.another-guard', config('auth.guards.web'));
    Filament::getCurrentOrDefaultPanel()->authGuard('another-guard');
    $this->actingAs($user, 'another-guard');

    $secondLimit = $rateLimiter(request());

    expect($firstLimit->key)->not->toBe($secondLimit->key)
        ->and($firstLimit->maxAttempts)->toBe(6)
        ->and($firstLimit->decaySeconds)->toBe(60);
});
