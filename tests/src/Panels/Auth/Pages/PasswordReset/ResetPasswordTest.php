<?php

use Filament\Auth\Pages\PasswordReset\ResetPassword;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    // Livewire's test headers apply only to mounting, not subsequent action requests.
    Event::listen(RouteMatched::class, function (RouteMatched $event): void {
        if ($clientIp = config('testing.reset_password_ip')) {
            $event->request->server->set('REMOTE_ADDR', $clientIp);
        }
    });
});

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

describe('resetting password', function (): void {
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
            ->assertNotified(
                Notification::make()
                    ->success()
                    ->title(__('passwords.reset'))
            )
            ->assertRedirect(Filament::getLoginUrl());

        Event::assertDispatched(PasswordReset::class);

        $this->assertCredentials([
            'email' => $userToResetPassword->email,
            'password' => 'new-password',
        ]);
    });

    it('cannot reset password without panel access', function (): void {
        Event::fake();

        $this->assertGuest();

        $userToResetPassword = User::factory()->create();
        $token = Password::createToken($userToResetPassword);

        Filament::setCurrentPanel(Filament::getPanel('custom'));

        livewire(ResetPassword::class, [
            'email' => $userToResetPassword->email,
            'token' => $token,
        ])
            ->set('password', 'new-password')
            ->set('passwordConfirmation', 'new-password')
            ->call('resetPassword')
            ->assertNotified(
                Notification::make()
                    ->danger()
                    ->title(__('passwords.user'))
            );

        Event::assertNotDispatched(PasswordReset::class);

        $this->assertCredentials([
            'email' => $userToResetPassword->email,
            'password' => 'password',
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

describe('validation', function (): void {
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
});

it('can throttle reset password attempts per email', function (): void {
    Event::fake([PasswordReset::class]);

    $this->assertGuest();

    $userToResetPassword = User::factory()->create();

    // Rotate request IPs to isolate the email-based rate limit.
    $rotateRequestIp = function (): void {
        static $requestNumber = 0;

        config()->set('testing.reset_password_ip', '192.0.2.' . ++$requestNumber);
    };

    foreach (range(1, 2) as $i) {
        $rotateRequestIp();

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

    $rotateRequestIp();

    // The 3rd attempt should be rate limited by email
    $token = Password::createToken($userToResetPassword);

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

    $rotateRequestIp();

    // A different email should not be affected
    $secondUser = User::factory()->create();
    $secondToken = Password::createToken($secondUser);

    livewire(ResetPassword::class, [
        'email' => $secondUser->email,
        'token' => $secondToken,
    ])
        ->set('password', 'new-password')
        ->set('passwordConfirmation', 'new-password')
        ->call('resetPassword')
        ->assertNotified()
        ->assertRedirect(Filament::getLoginUrl());

    Event::assertDispatchedTimes(PasswordReset::class, times: 3);
});

it('scopes reset password rate limits by password broker', function (): void {
    Event::fake([PasswordReset::class]);

    $defaultPasswordBroker = Password::getDefaultDriver();
    config()->set('auth.passwords.another-broker', config("auth.passwords.{$defaultPasswordBroker}"));
    $user = User::factory()->create();
    $token = Password::broker($defaultPasswordBroker)->createToken($user);
    $panel = Filament::getCurrentOrDefaultPanel();

    foreach ([null, $defaultPasswordBroker, $defaultPasswordBroker] as $attemptIndex => $broker) {
        $panel->authPasswordBroker($broker);

        config()->set('testing.reset_password_ip', '192.0.2.' . ($attemptIndex + 1));

        $resetPassword = livewire(ResetPassword::class, ['email' => $user->email, 'token' => $token])
            ->set('password', '')
            ->set('passwordConfirmation', 'new-password')
            ->call('resetPassword')
            ->assertNoRedirect();

        if ($attemptIndex < 2) {
            $resetPassword->assertHasErrors(['password' => ['required']]);
        } else {
            $resetPassword->assertHasNoErrors()->assertNotified(
                __('filament-panels::auth/pages/password-reset/reset-password.notifications.throttled.title')
            );
        }
    }

    Event::assertNotDispatched(PasswordReset::class);
    $this->assertCredentials(['email' => $user->email, 'password' => 'password']);

    $panel->authPasswordBroker('another-broker');

    config()->set('testing.reset_password_ip', '192.0.2.4');

    livewire(ResetPassword::class, ['email' => $user->email, 'token' => $token])
        ->set('password', 'new-password')
        ->set('passwordConfirmation', 'new-password')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertNotified(Notification::make()->success()->title(__('passwords.reset')))
        ->assertRedirect(Filament::getLoginUrl());

    Event::assertDispatchedTimes(PasswordReset::class, times: 1);
    $this->assertCredentials(['email' => $user->email, 'password' => 'new-password']);
});
