<?php

use Filament\Auth\Notifications\VerifyEmail;
use Filament\Auth\Pages\EmailVerification\EmailVerificationPrompt;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    expect(Filament::getEmailVerificationPromptUrl())->toEndWith('/email-verification/prompt');

    $this->get(Filament::getEmailVerificationPromptUrl())
        ->assertSuccessful();
});

it('can render page with a custom slug', function (): void {
    Filament::setCurrentPanel('slugs');

    expect(Filament::getEmailVerificationPromptUrl())->toEndWith('/email-verification-test/prompt-test');

    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    $this->get(Filament::getEmailVerificationPromptUrl())
        ->assertSuccessful();
});

it('can resend notification', function (): void {
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

it('can throttle resend notification attempts', function (): void {
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

it('can throttle resend notification attempts per user', function (): void {
    Notification::fake();

    $userToVerify = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($userToVerify);

    // Clear the IP-based rate limiter between attempts to isolate the
    // user-based rate limit (simulates an attacker rotating IPs).
    $clearIpRateLimiter = function (): void {
        RateLimiter::clear('livewire-rate-limiter:' . sha1(EmailVerificationPrompt::class . '|resendNotification|' . request()->ip()));
    };

    foreach (range(1, 2) as $i) {
        $clearIpRateLimiter();

        livewire(EmailVerificationPrompt::class)
            ->callAction('resendNotification')
            ->assertNotified();
    }

    Notification::assertSentToTimes($userToVerify, VerifyEmail::class, times: 2);

    $clearIpRateLimiter();

    // The 3rd attempt should be rate limited by user ID
    livewire(EmailVerificationPrompt::class)
        ->callAction('resendNotification')
        ->assertNotified();

    Notification::assertSentToTimes($userToVerify, VerifyEmail::class, times: 2);
});

it('redirects guests to the panel when unauthenticated', function (): void {
    $this->withoutMiddleware(Authenticate::class);

    $panel = Filament::getCurrentOrDefaultPanel();

    expect($panel)->not()->toBeNull();
    expect($panel->hasLogin())->toBeTrue();
    expect(Filament::getEmailVerificationPromptUrl())->not()->toBeNull();

    $this->get(Filament::getEmailVerificationPromptUrl())
        ->assertRedirect($panel?->getUrl());
});
