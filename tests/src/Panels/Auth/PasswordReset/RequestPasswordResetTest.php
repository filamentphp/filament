<?php

use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Notification;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function () {
    expect(Filament::getRequestPasswordResetUrl())->toEndWith('/password-reset/request');

    $this->get(Filament::getRequestPasswordResetUrl())
        ->assertSuccessful();
});

it('can render page with a custom slug', function () {
    Filament::setCurrentPanel(Filament::getPanel('slugs'));

    expect(Filament::getRequestPasswordResetUrl())->toEndWith('/password-reset-test/request-test');

    $this->get(Filament::getRequestPasswordResetUrl())
        ->assertSuccessful();
});

it('can request password reset', function () {
    Notification::fake();

    $this->assertGuest();

    $userToResetPassword = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => $userToResetPassword->email,
        ])
        ->call('request')
        ->assertNotified();

    Notification::assertSentTo($userToResetPassword, ResetPassword::class);
});

it('can throttle requests', function () {
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

it('can validate `email` is required', function () {
    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => '',
        ])
        ->call('request')
        ->assertHasFormErrors(['email' => ['required']]);
});

it('can validate `email` is valid email', function () {
    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => 'invalid-email',
        ])
        ->call('request')
        ->assertHasFormErrors(['email' => ['email']]);
});
