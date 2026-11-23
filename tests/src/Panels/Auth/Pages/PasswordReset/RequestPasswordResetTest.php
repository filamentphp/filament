<?php

use Filament\Auth\Notifications\ResetPassword;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Facades\Filament;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Sleep;

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

describe('requesting password reset', function (): void {
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

    it('does not send a queued password reset notification after the mail recipient changes', function (): void {
        config()->set('mail.default', 'array');

        Queue::fake();

        $userToResetPassword = User::factory()->create();

        livewire(RequestPasswordReset::class)
            ->fillForm([
                'email' => $userToResetPassword->email,
            ])
            ->call('request')
            ->assertNotified();

        $queuedNotification = Queue::pushed(SendQueuedNotifications::class)->sole();
        $serializedNotification = serialize($queuedNotification);

        $userToResetPassword->updateQuietly([
            'email' => 'changed@example.com',
        ]);

        unserialize($serializedNotification)->handle(app(ChannelManager::class));

        expect(app('mailer')->getSymfonyTransport()->messages())->toHaveCount(0);
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

    it('does not replace an existing password reset token without panel access', function (): void {
        Notification::fake();

        $this->assertGuest();

        $userToResetPassword = User::factory()->create();

        livewire(RequestPasswordReset::class)
            ->fillForm([
                'email' => $userToResetPassword->email,
            ])
            ->call('request');

        $resetPasswordNotification = Notification::sent($userToResetPassword, ResetPassword::class)->first();

        expect($resetPasswordNotification)
            ->toBeInstanceOf(ResetPassword::class)
            ->and(Password::broker(Filament::getAuthPasswordBroker())->tokenExists($userToResetPassword, $resetPasswordNotification->token))
            ->toBeTrue();

        $this->travel(61)->seconds();

        Filament::setCurrentPanel(Filament::getPanel('custom'));

        livewire(RequestPasswordReset::class)
            ->fillForm([
                'email' => $userToResetPassword->email,
            ])
            ->call('request');

        Notification::assertSentToTimes($userToResetPassword, ResetPassword::class, times: 1);

        expect(Password::broker(Filament::getAuthPasswordBroker())->tokenExists($userToResetPassword, $resetPasswordNotification->token))
            ->toBeTrue();
    });

    it('does not disclose panel access when a password reset token is throttled', function (string $panel): void {
        Notification::fake();

        $userToResetPassword = User::factory()->create();
        $broker = Password::broker(Filament::getAuthPasswordBroker());
        $token = $broker->createToken($userToResetPassword);

        Filament::setCurrentPanel($panel);

        foreach (range(1, 2) as $attempt) {
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
        }

        Notification::assertNotSentTo($userToResetPassword, ResetPassword::class);

        expect($broker->tokenExists($userToResetPassword, $token))->toBeTrue();
    })->with(['admin', 'custom']);

    it('pads password reset requests even when the broker does not', function (string $panel): void {
        Notification::fake();
        Sleep::fake();

        config()->set('auth.timebox_duration', 5_000_000);

        $userToResetPassword = User::factory()->create();
        $broker = Password::broker(Filament::getAuthPasswordBroker());
        $broker->createToken($userToResetPassword);

        // Laravel 11 brokers do not have their own `Timebox`.
        if (method_exists($broker, 'getTimebox')) {
            $broker->getTimebox()->returnEarly();
        }

        Filament::setCurrentPanel($panel);

        livewire(RequestPasswordReset::class)
            ->fillForm([
                'email' => $userToResetPassword->email,
            ])
            ->call('request');

        Sleep::assertSlept(fn ($duration): bool => $duration->totalMicroseconds >= 4_000_000);
    })->with(['admin', 'custom']);

    it('pads dispatch independently of a slow user lookup with broker timeboxing enabled', function (string $panel): void {
        Notification::fake();
        Sleep::fake();

        config()->set('auth.timebox_duration', 1_000_000);

        $userToResetPassword = User::factory()->create();
        Password::broker(Filament::getAuthPasswordBroker())->createToken($userToResetPassword);

        User::retrieved(static function (): void {
            usleep(250_000);
        });

        $padding = 0;

        Sleep::whenFakingSleep(function ($duration) use (&$padding): void {
            $padding += $duration->totalMicroseconds;
        });

        Filament::setCurrentPanel($panel);

        livewire(RequestPasswordReset::class)
            ->fillForm([
                'email' => $userToResetPassword->email,
            ])
            ->call('request');

        expect($padding)->toBeGreaterThanOrEqual(900_000);
    })->with(['admin', 'custom']);
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
