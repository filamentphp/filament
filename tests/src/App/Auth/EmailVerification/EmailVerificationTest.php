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
