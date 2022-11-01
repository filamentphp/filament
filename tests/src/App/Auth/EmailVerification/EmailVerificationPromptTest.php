<?php

use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render page', function () {
    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    $this->get(Filament::getEmailVerificationPromptUrl())->assertSuccessful();
});

it('can resend notification', function () {
    Notification::fake();

    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    livewire(EmailVerificationPrompt::class)
        ->call('resendNotification')
        ->assertNotified();

    Notification::assertSentTo($userToVerify, VerifyEmail::class);
});

it('can throttle resend notification attempts', function () {
    Notification::fake();

    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    livewire(EmailVerificationPrompt::class)
        ->call('resendNotification')
        ->assertNotified();

    Notification::assertSentToTimes($userToVerify, VerifyEmail::class, times: 1);

    livewire(EmailVerificationPrompt::class)
        ->call('resendNotification')
        ->assertNotified();

    Notification::assertSentToTimes($userToVerify, VerifyEmail::class, times: 1);
});
