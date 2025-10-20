<?php

use Filament\Auth\Notifications\ResetPassword;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Facades\Filament;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    expect(Filament::getRequestPasswordResetUrl())->toEndWith('/password-reset/request');

    $this->get(Filament::getRequestPasswordResetUrl())
        ->assertSuccessful();
});

it('can render page with a custom slug', function (): void {
    Filament::setCurrentPanel('slugs');

    expect(Filament::getRequestPasswordResetUrl())->toEndWith('/password-reset-test/request-test');

    $this->get(Filament::getRequestPasswordResetUrl())
        ->assertSuccessful();
});

it('can request password reset', function (): void {
    Notification::fake();
    Event::fake();

    $this->assertGuest();

    $userToResetPassword = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => $userToResetPassword->email,
        ])
        ->call('request')
        ->assertNotified(
            FilamentNotification::make()
                ->success()
                ->title(__('passwords.sent'))
                ->body(__('filament-panels::auth/pages/password-reset/request-password-reset.notifications.sent.body'))
        );

    Notification::assertSentTo($userToResetPassword, ResetPassword::class);

    if (class_exists(PasswordResetLinkSent::class)) {
        Event::assertDispatched(PasswordResetLinkSent::class, fn (PasswordResetLinkSent $event): bool => $event->user->is($userToResetPassword));
    }
});

it('can throttle requests', function (): void {
    Notification::fake();

    $this->assertGuest();

    foreach (range(1, 2) as $i) {
        $userToResetPassword = User::factory()->create();

        livewire(RequestPasswordReset::class)
            ->fillForm([
                'email' => $userToResetPassword->email,
            ])
            ->call('request')
            ->assertNotified();

        Notification::assertSentToTimes($userToResetPassword, ResetPassword::class, times: 1);
    }

    $userToResetPassword = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => $userToResetPassword->email,
        ])
        ->call('request')
        ->assertNotified();

    Notification::assertNotSentTo($userToResetPassword, ResetPassword::class);
});

it('cannot request password reset without panel access', function (): void {
    Notification::fake();

    $this->assertGuest();

    $userToResetPassword = User::factory()->create();

    Filament::setCurrentPanel(Filament::getPanel('custom'));

    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => $userToResetPassword->email,
        ])
        ->call('request')
        ->assertNotified(
            FilamentNotification::make()
                ->success()
                ->title(__('passwords.sent'))
                ->body(__('filament-panels::auth/pages/password-reset/request-password-reset.notifications.sent.body'))
        );

    Notification::assertNotSentTo($userToResetPassword, ResetPassword::class);
});

it('can validate `email` is required', function (): void {
    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => '',
        ])
        ->call('request')
        ->assertHasFormErrors(['email' => ['required']]);
});

it('can validate `email` is valid email', function (): void {
    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => 'invalid-email',
        ])
        ->call('request')
        ->assertHasFormErrors(['email' => ['email']]);
});
