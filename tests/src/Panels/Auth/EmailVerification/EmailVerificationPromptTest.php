<?php

use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Notification;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function () {
    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    expect(Filament::getEmailVerificationPromptUrl())->toEndWith('/email-verification/prompt');

    $this->get(Filament::getEmailVerificationPromptUrl())
        ->assertSuccessful();
});

it('can render page with a custom slug', function () {
    Filament::setCurrentPanel(Filament::getPanel('slugs'));

    expect(Filament::getEmailVerificationPromptUrl())->toEndWith('/email-verification-test/prompt-test');

    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    $this->get(Filament::getEmailVerificationPromptUrl())
        ->assertSuccessful();
});

it('can resend notification', function () {
    Notification::fake();

    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    livewire(EmailVerificationPrompt::class)
        ->callAction('resendNotification')
        ->assertNotified();

    Notification::assertSentTo($userToVerify, VerifyEmail::class);
});

it('can throttle resend notification attempts', function () {
    Notification::fake();

    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    foreach (range(1, 2) as $i) {
        livewire(EmailVerificationPrompt::class)
            ->callAction('resendNotification')
            ->assertNotified();
    }

    Notification::assertSentToTimes($userToVerify, VerifyEmail::class, times: 2);

    livewire(EmailVerificationPrompt::class)
        ->callAction('resendNotification')
        ->assertNotified();

    Notification::assertSentToTimes($userToVerify, VerifyEmail::class, times: 2);
});
