<?php

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use League\Uri\Components\Query;

uses(TestCase::class);

it('can verify an email change', function (): void {
    $userToVerify = User::factory()->create();
    $newEmail = fake()->email;

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put($verificationSignature, true, ttl: now()->addHour());

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    Notification::assertNotified('Email address changed');

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);
});

it('can verify an email with a custom slug', function (): void {
    Filament::setCurrentPanel('slugs');

    $userToVerify = User::factory()->create();
    $newEmail = fake()->email;

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put($verificationSignature, true, ttl: now()->addHour());

    expect($userToVerify)
        ->email->not->toBe($newEmail)
        ->and($verificationUrl)->toContain('/email-change-verification-test/verify-change-test/');

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);
});

it('cannot verify an email when signed in as another user', function (): void {
    $userToVerify = User::factory()->create();
    $newEmail = fake()->email;

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put($verificationSignature, true, ttl: now()->addHour());

    $anotherUser = User::factory()->create();

    $this
        ->actingAs($anotherUser)
        ->get($verificationUrl)
        ->assertForbidden();

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);
});

it('cannot verify an email change with the same URL twice', function (): void {
    $userToVerify = User::factory()->create();
    $newEmail = fake()->email;

    expect($userToVerify->refresh())
        ->email->not->toBe($newEmail);

    $verificationUrl = Filament::getVerifyEmailChangeUrl($userToVerify, $newEmail);

    $verificationSignature = Query::new($verificationUrl)->get('signature');
    cache()->put($verificationSignature, true, ttl: now()->addHour());

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertRedirect(Filament::getProfileUrl());

    expect($userToVerify->refresh())
        ->email->toBe($newEmail);

    expect(cache()->has($verificationSignature))
        ->toBeFalse();

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertForbidden();

    $userToVerify->update(['email' => fake()->email]);

    $this
        ->actingAs($userToVerify)
        ->get($verificationUrl)
        ->assertForbidden();
});
