<?php

use Filament\Auth\Notifications\NoticeOfEmailChangeRequest;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Auth\Notifications\VerifyEmailChange;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Tests\Fixtures\Models\ProfileEmailChangeMailUser;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\EditProfileWithCustomRedirect;
use Filament\Tests\Fixtures\Pages\EditProfileWithOptionalEmailVerification;
use Filament\Tests\TestCase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use League\Uri\Components\Query;
use Livewire\Mechanisms\ComponentRegistry;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::getCurrentOrDefaultPanel()
        ->profile(EditProfile::class)
        ->emailChangeVerification(false)
        ->emailVerification(null, isRequired: false);

    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

it('can render page', function (): void {
    $this->get(Filament::getProfileUrl())
        ->assertSuccessful();
});

it('can retrieve data', function (): void {
    livewire(EditProfile::class)
        ->assertSchemaStateSet([
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);
});

describe('saving profile', function (): void {
    it('can save name', function (): void {
        $newUserData = User::factory()->make();

        livewire(EditProfile::class)
            ->fillForm([
                'name' => $newUserData->name,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Saved');

        expect($this->user->refresh())
            ->name->toBe($newUserData->name);
    });

    it('can save email', function (): void {
        Filament::getCurrentOrDefaultPanel()->emailChangeVerification(false);

        $newUserData = User::factory()->make();

        livewire(EditProfile::class)
            ->fillForm([
                'email' => $newUserData->email,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Saved');

        expect($this->user->refresh())
            ->email->toBe($newUserData->email);
    });

    it('marks a changed email address as unverified', function (): void {
        Filament::getCurrentOrDefaultPanel()->emailChangeVerification(false);

        $newUserData = User::factory()->make();

        livewire(EditProfile::class)
            ->fillForm([
                'email' => $newUserData->email,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Saved');

        expect($this->user->refresh())
            ->email->toBe($newUserData->email)
            ->hasVerifiedEmail()->toBeFalse();
    });

    it('does not send verification or report success when a model observer cancels the email change', function (bool $hasDatabaseTransactions): void {
        Notification::fake();

        Filament::getCurrentOrDefaultPanel()
            ->databaseTransactions($hasDatabaseTransactions)
            ->emailVerification();

        $oldEmail = $this->user->email;
        $transactionLevel = DB::transactionLevel();

        User::updating(static fn (): bool => false);

        livewire(EditProfile::class)
            ->fillForm([
                'email' => 'rejected@example.com',
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotNotified('Saved')
            ->assertNoRedirect();

        Notification::assertNothingSent();

        expect($this->user)->email->toBe($oldEmail)
            ->hasVerifiedEmail()->toBeTrue()
            ->and($this->user->fresh())->email->toBe($oldEmail)
            ->hasVerifiedEmail()->toBeTrue()
            ->and(DB::transactionLevel())->toBe($transactionLevel);
    })->with([false, true]);

    it('does not mark an unchanged email address as unverified', function (): void {
        Notification::fake();

        Filament::getCurrentOrDefaultPanel()->emailVerification(isRequired: false);

        livewire(EditProfile::class)
            ->fillForm([
                'name' => fake()->name(),
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Saved');

        expect($this->user->refresh())
            ->hasVerifiedEmail()->toBeTrue();

        Notification::assertNothingSent();
    });

    it('only preserves verification for the original normalized email', function (bool $hasConcurrentEmailChange): void {
        Notification::fake();

        Filament::getCurrentOrDefaultPanel()->emailVerification();

        $this->user->updateQuietly(['email' => 'user@example.com']);
        $verifiedAt = $this->user->refresh()->email_verified_at;

        User::updating(static function (User $user): void {
            $user->email = strtolower($user->email);
        });

        if ($hasConcurrentEmailChange) {
            User::updated(static function (User $user): void {
                $user->newQuery()->whereKey($user->getKey())->update([
                    'email' => 'other@example.com',
                    'email_verified_at' => null,
                ]);
            });
        }

        $livewire = livewire(EditProfile::class)
            ->fillForm([
                'email' => 'User@example.com',
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Saved');

        expect($this->user->refresh())->email->toBe($hasConcurrentEmailChange ? 'other@example.com' : 'user@example.com')
            ->email_verified_at->toEqual($hasConcurrentEmailChange ? null : $verifiedAt);

        if ($hasConcurrentEmailChange) {
            $livewire->assertRedirect(Filament::getEmailVerificationPromptUrl());
            Notification::assertSentTo($this->user, VerifyEmail::class);
        } else {
            $livewire->assertNoRedirect();
            Notification::assertNothingSent();
        }
    })->with([false, true]);

    it('redirects to email verification when the profile page requires it', function (): void {
        Filament::getCurrentOrDefaultPanel()
            ->emailChangeVerification(false)
            ->emailVerification();

        livewire(EditProfile::class)
            ->fillForm([
                'email' => User::factory()->make()->email,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(Filament::getEmailVerificationPromptUrl());
    });

    it('does not redirect to email verification when the panel does not require it', function (): void {
        Notification::fake();

        Filament::getCurrentOrDefaultPanel()->emailVerification(isRequired: false);

        livewire(EditProfile::class)
            ->fillForm([
                'email' => User::factory()->make()->email,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNoRedirect();
    });

    it('sends email verification when the feature is enabled', function (): void {
        Notification::fake();

        Filament::getCurrentOrDefaultPanel()->emailVerification(isRequired: false);

        livewire(EditProfile::class)
            ->fillForm([
                'email' => User::factory()->make()->email,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        Notification::assertSentTo($this->user, VerifyEmail::class, function (VerifyEmail $notification): bool {
            return filled($notification->url);
        });
    });

    it('keeps the current session authenticated when email verification delivery fails after a password change', function (): void {
        Filament::getCurrentOrDefaultPanel()->emailVerification(isRequired: false);

        Filament::auth()->login($this->user);

        $response = $this->get(Filament::getProfileUrl())->assertSuccessful();
        $originalSessionPasswordHash = session('password_hash_' . Filament::getAuthGuard());
        $snapshot = collect(Str::matchAll('/wire:snapshot="([^"]+)"/', $response->getContent()))
            ->map(static fn (string $snapshot): string => htmlspecialchars_decode($snapshot, ENT_QUOTES))
            ->first(static fn (string $snapshot): bool => json_decode($snapshot, associative: true)['memo']['name'] === app(ComponentRegistry::class)->getName(EditProfile::class));

        Event::listen(NotificationSending::class, static function (NotificationSending $event): void {
            if ($event->notification instanceof VerifyEmail) {
                throw new RuntimeException('Verification delivery failed.');
            }
        });

        $newPassword = Str::random();

        $failureResponse = $this->withExceptionHandling()
            ->withHeaders(['X-Livewire' => true])
            ->postJson(app('livewire')->getUpdateUri(), [
                'components' => [[
                    'snapshot' => $snapshot,
                    'updates' => [
                        'data.email' => 'changed@example.com',
                        'data.password' => $newPassword,
                        'data.passwordConfirmation' => $newPassword,
                        'data.currentPassword' => 'password',
                    ],
                    'calls' => [['method' => 'save', 'params' => [], 'path' => '']],
                ]],
            ])
            ->assertStatus(500);

        expect($failureResponse->exception?->getMessage())->toBe('Verification delivery failed.');

        expect($this->user->fresh())->email->toBe('changed@example.com');
        expect(Hash::check($newPassword, $this->user->fresh()->getAuthPassword()))->toBeTrue();
        expect(session('password_hash_' . Filament::getAuthGuard()))->not->toBe($originalSessionPasswordHash);

        Route::middleware(['web', 'auth:' . Filament::getAuthGuard(), AuthenticateSession::class])
            ->get('/profile-session-check', static fn () => response()->noContent());

        app('auth')->forgetGuards();

        $this->get('/profile-session-check')->assertNoContent();
        $this->assertAuthenticatedAs($this->user, Filament::getAuthGuard());
    });

    it('does not send email verification when the feature is disabled', function (): void {
        Notification::fake();

        livewire(EditProfile::class)
            ->fillForm([
                'email' => User::factory()->make()->email,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        Notification::assertNothingSent();
    });

    it('does not redirect to email verification when the profile page does not require it', function (): void {
        Filament::getCurrentOrDefaultPanel()
            ->profile(EditProfileWithOptionalEmailVerification::class)
            ->emailChangeVerification(false)
            ->emailVerification();

        livewire(EditProfileWithOptionalEmailVerification::class)
            ->fillForm([
                'email' => User::factory()->make()->email,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNoRedirect();
    });

    it('redirects to email verification before a custom profile redirect', function (): void {
        Filament::getCurrentOrDefaultPanel()
            ->profile(EditProfileWithCustomRedirect::class)
            ->emailChangeVerification(false)
            ->emailVerification();

        livewire(EditProfileWithCustomRedirect::class)
            ->fillForm([
                'email' => User::factory()->make()->email,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(Filament::getEmailVerificationPromptUrl());
    });

    it('uses a custom profile redirect when email verification is not required', function (): void {
        Filament::getCurrentOrDefaultPanel()
            ->profile(EditProfileWithCustomRedirect::class)
            ->emailVerification(null, isRequired: false);

        livewire(EditProfileWithCustomRedirect::class)
            ->fillForm([
                'name' => fake()->name(),
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect('/custom-redirect');
    });

    it('can send email change verification', function (): void {
        Notification::fake();

        Filament::getCurrentOrDefaultPanel()->emailChangeVerification();

        $oldEmail = $this->user->email;

        $newUserData = User::factory()->make();

        livewire(EditProfile::class)
            ->fillForm([
                'email' => $newUserData->email,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Email address change request sent')
            ->assertNoRedirect()
            ->assertSchemaStateSet([
                'email' => $oldEmail,
            ]);

        expect($this->user->refresh())
            ->email->toBe($oldEmail)
            ->hasVerifiedEmail()->toBeTrue();

        Notification::assertNotSentTo($this->user, VerifyEmail::class);

        Notification::assertSentOnDemand(NoticeOfEmailChangeRequest::class, function (NoticeOfEmailChangeRequest $notification, array $channels, AnonymousNotifiable $notifiable) use ($newUserData, $oldEmail): bool {
            if (blank($notification->blockVerificationUrl)) {
                return false;
            }

            return ($notification->newEmail === $newUserData->email) && ($notifiable->routeNotificationFor('mail') === $oldEmail);
        });

        Notification::assertSentOnDemand(VerifyEmailChange::class, function (VerifyEmailChange $notification): bool {
            $verificationSignature = Query::new($notification->url)->get('signature');

            return cache()->has("filament:auth:email_change:verification:{$verificationSignature}") && (! cache()->has($verificationSignature));
        });
    });

    it('keeps the queued email change notice addressed to the original mailbox', function (bool $hasCustomMailRoute): void {
        Queue::fake();

        Filament::getCurrentOrDefaultPanel()->emailChangeVerification();

        if ($hasCustomMailRoute) {
            $this->user = ProfileEmailChangeMailUser::query()->findOrFail($this->user->getKey());
            $this->actingAs($this->user);
        }

        $oldEmail = $this->user->email;
        $oldRecipient = $hasCustomMailRoute ? [$oldEmail => $this->user->name] : $oldEmail;
        $newEmail = 'changed@example.com';

        livewire(EditProfile::class)
            ->fillForm([
                'email' => $newEmail,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $noticeJob = Queue::pushed(SendQueuedNotifications::class)
            ->first(static fn (SendQueuedNotifications $job): bool => $job->notification instanceof NoticeOfEmailChangeRequest);

        $serializedJob = serialize($noticeJob);

        $this->user->update(['email' => $newEmail]);

        $restoredJob = unserialize($serializedJob);

        expect($restoredJob->notifiables->first()->routeNotificationFor('mail', $restoredJob->notification))
            ->toBe($oldRecipient);

        if ($hasCustomMailRoute) {
            expect($restoredJob->notification->locale)->toBe('fr');
        }
    })->with([false, true]);

    it('can verify an email change throughout the configured verification lifetime', function (): void {
        Notification::fake();

        config()->set('auth.verification.expire', 120);
        Filament::getCurrentOrDefaultPanel()->emailChangeVerification();

        $newEmail = 'changed@example.com';

        livewire(EditProfile::class)
            ->fillForm([
                'email' => $newEmail,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $verification = Notification::sent(new AnonymousNotifiable, VerifyEmailChange::class)->sole();

        $this->travel(61)->minutes();

        $this->get($verification->url)->assertRedirect(Filament::getProfileUrl());

        expect($this->user->refresh())->email->toBe($newEmail);
    });

    it('can save password', function (): void {
        expect(Filament::auth()->attempt([
            'email' => $this->user->email,
            'password' => 'password',
        ]))->toBeTrue();

        $newPassword = Str::random();

        livewire(EditProfile::class)
            ->fillForm([
                'password' => $newPassword,
                'passwordConfirmation' => $newPassword,
                'currentPassword' => 'password',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Saved')
            ->assertSchemaStateSet([
                'password' => '',
                'passwordConfirmation' => '',
            ]);

        expect(Filament::auth()->attempt([
            'email' => $this->user->email,
            'password' => 'password',
        ]))->toBeFalse();

        expect(Filament::auth()->attempt([
            'email' => $this->user->email,
            'password' => $newPassword,
        ]))->toBeTrue();
    });

});

describe('rate limiting', function (): void {
    it('can throttle profile save attempts', function (): void {
        $newUserData = User::factory()->make();

        foreach (range(1, 5) as $i) {
            livewire(EditProfile::class)
                ->fillForm([
                    'name' => $newUserData->name,
                ])
                ->call('save')
                ->assertHasNoFormErrors()
                ->assertNotified('Saved');
        }

        livewire(EditProfile::class)
            ->fillForm([
                'name' => $newUserData->name,
            ])
            ->call('save')
            ->assertNotified();

        // The 6th attempt should be throttled and name should remain unchanged from previous save
        expect($this->user->refresh())
            ->name->toBe($newUserData->name);
    });

    it('can throttle profile save attempts per user', function (): void {
        $newUserData = User::factory()->make();
        $originalName = $this->user->name;

        // Clear the IP-based rate limiter between attempts to isolate the
        // user-based rate limit (simulates an attacker rotating IPs).
        $clearIpRateLimiter = function (): void {
            RateLimiter::clear('livewire-rate-limiter:' . sha1(EditProfile::class . '|save|' . request()->ip()));
        };

        foreach (range(1, 5) as $i) {
            $clearIpRateLimiter();

            livewire(EditProfile::class)
                ->fillForm([
                    'name' => $newUserData->name,
                ])
                ->call('save')
                ->assertHasNoFormErrors()
                ->assertNotified('Saved');
        }

        $clearIpRateLimiter();

        $anotherName = fake()->name();

        // The 6th attempt should be rate limited by user ID
        livewire(EditProfile::class)
            ->fillForm([
                'name' => $anotherName,
            ])
            ->call('save')
            ->assertNotified();

        // Name should not have changed to the new value
        expect($this->user->refresh())
            ->name->not->toBe($anotherName);
    });

});

it('can validate', function (array $formData, array $errors): void {
    Filament::getCurrentOrDefaultPanel()->emailChangeVerification(false);

    livewire(EditProfile::class)
        ->fillForm($formData)
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNotNotified('Saved');

    $this->user->refresh();

    foreach ($formData as $key => $value) {
        expect($this->user->getAttributeValue($key))
            ->not->toBe($value);
    }
})->with([
    '`name` is required' => [
        ['name' => '', 'email' => fake()->email()],
        ['name' => ['required']],
    ],
    '`name` is max 255 characters' => [
        ['name' => Str::random(256), 'email' => fake()->email()],
        ['name' => ['max']],
    ],
    '`email` is required' => [
        ['name' => fake()->name(), 'email' => ''],
        ['email' => ['required']],
    ],
    '`email` is valid email address' => [
        ['name' => fake()->name(), 'email' => 'not-an-email'],
        ['email' => ['email']],
    ],
    '`email` does not contain angle brackets' => [
        ['name' => fake()->name(), 'email' => '"<attacker@example.com>"@trusted.example'],
        ['email' => ['not_regex']],
    ],
    '`email` is unique' => fn (): array => [
        ['name' => fake()->name(), 'email' => User::factory()->create()->email],
        ['email' => ['unique']],
    ],
    '`password` is confirmed' => fn (): array => [
        ['name' => fake()->name(), 'email' => fake()->email(), 'password' => Str::random(), 'passwordConfirmation' => Str::random()],
        ['password' => ['same']],
    ],
    '`passwordConfirmation` is required when `password` is filled' => fn (): array => [
        ['name' => fake()->name(), 'email' => fake()->email(), 'password' => Str::random(), 'passwordConfirmation' => ''],
        ['passwordConfirmation' => ['required']],
    ],
    '`currentPassword` is required when `password` is filled' => fn (): array => [
        ['name' => fake()->name(), 'email' => fake()->email(), 'password' => Str::random(), 'currentPassword' => ''],
        ['currentPassword' => ['required']],
    ],
    '`currentPassword` is required when `email` is changed' => fn (): array => [
        ['name' => fake()->name(), 'email' => fake()->email(), 'currentPassword' => ''],
        ['currentPassword' => ['required']],
    ],
    '`currentPassword` is valid password' => fn (): array => [
        ['name' => fake()->name(), 'email' => fake()->email(), 'currentPassword' => 'invalid-password'],
        ['currentPassword' => ['current_password']],
    ],
]);
