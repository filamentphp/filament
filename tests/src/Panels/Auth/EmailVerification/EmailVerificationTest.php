<?php

use Filament\Facades\Filament;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can verify an email', function () {
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

it('can verify an email with a custom slug', function () {
    Filament::setCurrentPanel(Filament::getPanel('slugs'));

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

it('cannot verify an email when signed in as another user', function () {
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
