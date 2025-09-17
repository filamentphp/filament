<?php

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use League\Uri\Components\Query;

uses(TestCase::class);

it('can block an email change', function (): void {
    $userToVerify = User::factory()->create();
    $newEmail = fake()->email;

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put($verificationSignature, true, ttl: now()->addHour());

    $blockVerificationUrl = Filament::getBlockEmailChangeVerificationUrl($userToVerify, $newEmail, $verificationSignature);

    $this
        ->actingAs($userToVerify)
        ->get($blockVerificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    Notification::assertNotified('Email address change blocked');

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    expect(cache()->has($verificationSignature))
        ->toBeFalse();

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertForbidden();

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);
});

it('cannot block an email change when signed in as another user', function (): void {
    $userToVerify = User::factory()->create();
    $newEmail = fake()->email;

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put($verificationSignature, true, ttl: now()->addHour());

    $blockVerificationUrl = Filament::getBlockEmailChangeVerificationUrl($userToVerify, $newEmail, $verificationSignature);

    $anotherUser = User::factory()->create();

    $this
        ->actingAs($anotherUser)
        ->get($blockVerificationUrl)
        ->assertForbidden();

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);
});

it('cannot block an email change once it has been verified', function (): void {
    $userToVerify = User::factory()->create();
    $newEmail = fake()->email;

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put($verificationSignature, true, ttl: now()->addHour());

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);

    $blockVerificationUrl = Filament::getBlockEmailChangeVerificationUrl($userToVerify, $newEmail, $verificationSignature);

    $this
        ->actingAs($userToVerify)
        ->get($blockVerificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    Notification::assertNotified('Failed to block email address change');

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);
});
