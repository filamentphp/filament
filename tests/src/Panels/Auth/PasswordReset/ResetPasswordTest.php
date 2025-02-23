<?php

use Filament\Auth\Pages\PasswordReset\ResetPassword;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    $userToResetPassword = User::factory()->make();
    $token = Password::createToken($userToResetPassword);

    $url = Filament::getResetPasswordUrl(
        $token,
        $userToResetPassword,
    );

    expect($url)->toContain('/password-reset/reset');

    $this->get($url)->assertSuccessful();
});

it('can render page with a custom slug', function (): void {
    Filament::setCurrentPanel('slugs');

    $userToResetPassword = User::factory()->make();
    $token = Password::createToken($userToResetPassword);

    $url = Filament::getResetPasswordUrl(
        $token,
        $userToResetPassword,
    );

    expect($url)->toContain('/password-reset-test/reset-test');

    $this->get($url)->assertSuccessful();
});

it('can reset password', function (): void {
    Event::fake();

    $this->assertGuest();

    $userToResetPassword = User::factory()->create();
    $token = Password::createToken($userToResetPassword);

    livewire(ResetPassword::class, [
        'email' => $userToResetPassword->email,
        'token' => $token,
    ])
        ->set('password', 'new-password')
        ->set('passwordConfirmation', 'new-password')
        ->call('resetPassword')
        ->assertNotified()
        ->assertRedirect(Filament::getLoginUrl());

    Event::assertDispatched(PasswordReset::class);

    $this->assertCredentials([
        'email' => $userToResetPassword->email,
        'password' => 'new-password',
    ]);
});

it('requires request signature', function (): void {
    $userToResetPassword = User::factory()->make();
    $token = Password::createToken($userToResetPassword);

    $this->get(route('filament.admin.auth.password-reset.reset', [
        'email' => $userToResetPassword->getEmailForPasswordReset(),
        'token' => $token,
    ]))->assertForbidden();
});

it('requires valid email and token', function (): void {
    Event::fake();

    $this->assertGuest();

    $userToResetPassword = User::factory()->create();
    $token = Password::createToken($userToResetPassword);

    livewire(ResetPassword::class, [
        'email' => $userToResetPassword->email,
        'token' => Str::random(),
    ])
        ->set('password', 'new-password')
        ->set('passwordConfirmation', 'new-password')
        ->call('resetPassword')
        ->assertNotified()
        ->assertNoRedirect();

    Event::assertNotDispatched(PasswordReset::class);

    livewire(ResetPassword::class, [
        'email' => fake()->email(),
        'token' => $token,
    ])
        ->set('password', 'new-password')
        ->set('passwordConfirmation', 'new-password')
        ->call('resetPassword')
        ->assertNotified()
        ->assertNoRedirect();

    Event::assertNotDispatched(PasswordReset::class);
});

it('can throttle reset password attempts', function (): void {
    Event::fake();

    $this->assertGuest();

    foreach (range(1, 2) as $i) {
        $userToResetPassword = User::factory()->create();
        $token = Password::createToken($userToResetPassword);

        livewire(ResetPassword::class, [
            'email' => $userToResetPassword->email,
            'token' => $token,
        ])
            ->set('password', 'new-password')
            ->set('passwordConfirmation', 'new-password')
            ->call('resetPassword')
            ->assertNotified()
            ->assertRedirect(Filament::getLoginUrl());
    }

    Event::assertDispatchedTimes(PasswordReset::class, times: 2);

    $this->assertCredentials([
        'email' => $userToResetPassword->email,
        'password' => 'new-password',
    ]);

    livewire(ResetPassword::class, [
        'email' => $userToResetPassword->email,
        'token' => $token,
    ])
        ->set('password', 'newer-password')
        ->set('passwordConfirmation', 'newer-password')
        ->call('resetPassword')
        ->assertNotified()
        ->assertNoRedirect();

    Event::assertDispatchedTimes(PasswordReset::class, times: 2);

    $this->assertCredentials([
        'email' => $userToResetPassword->email,
        'password' => 'new-password',
    ]);
});

it('can validate `password` is required', function (): void {
    livewire(ResetPassword::class)
        ->set('password', '')
        ->call('resetPassword')
        ->assertHasErrors(['password' => ['required']]);
});

it('can validate `password` is confirmed', function (): void {
    livewire(ResetPassword::class)
        ->set('password', Str::random())
        ->set('passwordConfirmation', Str::random())
        ->call('resetPassword')
        ->assertHasErrors(['password' => ['same']]);
});

it('can validate `passwordConfirmation` is required', function (): void {
    livewire(ResetPassword::class)
        ->set('passwordConfirmation', '')
        ->call('resetPassword')
        ->assertHasErrors(['passwordConfirmation' => ['required']]);
});
