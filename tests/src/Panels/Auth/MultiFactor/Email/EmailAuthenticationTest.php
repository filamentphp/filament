<?php

use Filament\Actions\Testing\TestAction;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Panel;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Cache\ArrayLock;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Lock;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel('email-authentication');

    Notification::fake();
});

describe('authentication flow', function (): void {
    it('can render the challenge form after valid login credentials are successfully used', function (): void {
        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $emailAuthentication->generateCodesUsing(fn (): string => $code);

        $livewire = livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->assertSet('userUndertakingMultiFactorAuthentication', null)
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->assertFormFieldExists(
                "{$emailAuthentication->getId()}.code",
                'multiFactorChallengeForm',
                fn (OneTimeCodeInput $field): bool => $field->shouldSubmitOnCompletion(),
            );

        expect(decrypt($livewire->instance()->userUndertakingMultiFactorAuthentication))
            ->toBe([
                'identifier' => $userToAuthenticate->getAuthIdentifier(),
                'userKey' => Filament::getUserScopedAuthIdentifier($userToAuthenticate),
            ]);

        $this->assertGuest();

        Notification::assertSentTo($userToAuthenticate, VerifyEmailAuthentication::class, function (VerifyEmailAuthentication $notification) use ($code, $emailAuthentication): bool {
            if ($notification->codeExpiryMinutes !== $emailAuthentication->getCodeExpiryMinutes()) {
                return false;
            }

            return $notification->code === $code;
        });
    });

    it('will authenticate the user after a valid challenge code is used', function (): void {
        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $emailAuthentication->generateCodesUsing(fn (): string => $code);

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => $code,
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($userToAuthenticate);
    });

    it('can resend the code to the user', function (): void {
        $this->travelTo(now()->subMinute());

        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $livewire = livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate');

        Notification::assertSentTimes(VerifyEmailAuthentication::class, 1);

        $this->travelBack();

        $livewire
            ->callAction(
                TestAction::make('resend')
                    ->schemaComponent("{$emailAuthentication->getId()}.code", schema: 'multiFactorChallengeForm')
            )->assertNotified(
                FilamentNotification::make()
                    ->title(__('filament-panels::auth/multi-factor/email/provider.login_form.code.actions.resend.notifications.resent.title'))
                    ->success()
            );

        Notification::assertSentTimes(VerifyEmailAuthentication::class, 2);
    });

    it('can not resend the code to the user more than twice per minute', function (): void {
        $this->travelTo(now()->subMinute());

        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $livewire = livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate');

        Notification::assertSentTimes(VerifyEmailAuthentication::class, 1);

        $livewire
            ->callAction(
                TestAction::make('resend')
                    ->schemaComponent("{$emailAuthentication->getId()}.code", schema: 'multiFactorChallengeForm')
            )->assertNotified(
                FilamentNotification::make()
                    ->title(__('filament-panels::auth/multi-factor/email/provider.login_form.code.actions.resend.notifications.resent.title'))
                    ->success()
            );

        Notification::assertSentTimes(VerifyEmailAuthentication::class, 2);

        $livewire
            ->callAction(
                TestAction::make('resend')
                    ->schemaComponent("{$emailAuthentication->getId()}.code", schema: 'multiFactorChallengeForm')
            )->assertNotified(
                FilamentNotification::make()
                    ->title(__('filament-panels::auth/multi-factor/email/provider.login_form.code.actions.resend.notifications.throttled.title'))
                    ->danger()
            );

        Notification::assertSentTimes(VerifyEmailAuthentication::class, 2);

        $this->travelBack();

        $livewire
            ->callAction(
                TestAction::make('resend')
                    ->schemaComponent("{$emailAuthentication->getId()}.code", schema: 'multiFactorChallengeForm')
            )->assertNotified(
                FilamentNotification::make()
                    ->title(__('filament-panels::auth/multi-factor/email/provider.login_form.code.actions.resend.notifications.resent.title'))
                    ->success()
            );

        Notification::assertSentTimes(VerifyEmailAuthentication::class, 3);
    });
});

describe('failure cases', function (): void {
    it('will not render the challenge form after invalid login credentials are used', function (): void {
        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'incorrect-password',
            ])
            ->assertSet('userUndertakingMultiFactorAuthentication', null)
            ->call('authenticate')
            ->assertSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect();

        $this->assertGuest();

        Notification::assertNotSentTo($userToAuthenticate, VerifyEmailAuthentication::class);
    });

    it('will not render the challenge form if a user does not have multi-factor authentication enabled', function (): void {
        $userToAuthenticate = User::factory()->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->assertSet('userUndertakingMultiFactorAuthentication', null)
            ->call('authenticate')
            ->assertSet('userUndertakingMultiFactorAuthentication', null)
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($userToAuthenticate);

        Notification::assertNotSentTo($userToAuthenticate, VerifyEmailAuthentication::class);
    });

    it('will not authenticate the user when an invalid challenge code is used', function (): void {
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$emailAuthentication->getId()}.code",
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    it('will not verify an email code from a stale session after it has been consumed', function (): void {
        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $code = '123456';
        $emailAuthentication->generateCodesUsing(static fn (): string => $code);
        $emailAuthentication->sendCode($userToAuthenticate);

        $staleSessionData = session()->all();

        expect($emailAuthentication->verifyCode($code, $userToAuthenticate))->toBeTrue();

        session()->replace($staleSessionData);

        expect($emailAuthentication->verifyCode($code, $userToAuthenticate))->toBeFalse();
    });

    it('keeps the latest email code usable when stale session data is persisted', function (): void {
        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $issuedCodes = ['123456', '654321'];
        $emailAuthentication->generateCodesUsing(function () use (&$issuedCodes): string {
            return array_shift($issuedCodes);
        });

        expect($emailAuthentication->sendCode($userToAuthenticate))->toBeTrue();

        $staleSessionData = session()->all();

        expect($emailAuthentication->sendCode($userToAuthenticate))->toBeTrue();
        session()->replace($staleSessionData);

        expect($emailAuthentication->verifyCode('123456', $userToAuthenticate))->toBeFalse()
            ->and($emailAuthentication->verifyCode('654321', $userToAuthenticate))->toBeTrue()
            ->and($emailAuthentication->verifyCode('654321', $userToAuthenticate))->toBeFalse();
    });

    it('does not overwrite a newer email code after a cache lock expires', function (): void {
        $cacheStore = new OverlappingEmailAuthenticationCacheStore;

        Cache::extend('overlapping-email-authentication', fn (): Repository => new Repository($cacheStore));
        config()->set('cache.default', 'overlapping-email-authentication');
        config()->set('cache.stores.overlapping-email-authentication', ['driver' => 'overlapping-email-authentication']);

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $olderEmailAuthentication = EmailAuthentication::make()
            ->generateCodesUsing(static fn (): string => '123456');
        $newerEmailAuthentication = EmailAuthentication::make()
            ->generateCodesUsing(static fn (): string => '654321');
        $newerCodeWasSent = null;

        $cacheStore->afterLockAcquired(function () use ($newerEmailAuthentication, $userToAuthenticate, &$newerCodeWasSent): void {
            $this->travel(61)->seconds();

            $newerCodeWasSent = $newerEmailAuthentication->sendCode($userToAuthenticate);
        });

        expect($olderEmailAuthentication->sendCode($userToAuthenticate))->toBeFalse()
            ->and($newerCodeWasSent)->toBeTrue()
            ->and($olderEmailAuthentication->verifyCode('123456', $userToAuthenticate))->toBeFalse()
            ->and($newerEmailAuthentication->verifyCode('654321', $userToAuthenticate))->toBeTrue();
    });

    it('does not delete a newer email code after a cache lock expires', function (): void {
        $cacheStore = new OverlappingEmailAuthenticationCacheStore;

        Cache::extend('overlapping-email-authentication', fn (): Repository => new Repository($cacheStore));
        config()->set('cache.default', 'overlapping-email-authentication');
        config()->set('cache.stores.overlapping-email-authentication', ['driver' => 'overlapping-email-authentication']);

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $olderEmailAuthentication = EmailAuthentication::make()
            ->generateCodesUsing(static fn (): string => '123456');
        $newerEmailAuthentication = EmailAuthentication::make()
            ->generateCodesUsing(static fn (): string => '654321');

        expect($olderEmailAuthentication->sendCode($userToAuthenticate))->toBeTrue();

        $newerCodeWasSent = null;

        $cacheStore->beforeReadReturns(function () use ($newerEmailAuthentication, $userToAuthenticate, &$newerCodeWasSent): void {
            $this->travel(61)->seconds();

            $newerCodeWasSent = $newerEmailAuthentication->sendCode($userToAuthenticate);
        });

        expect($olderEmailAuthentication->verifyCode('123456', $userToAuthenticate))->toBeFalse()
            ->and($newerCodeWasSent)->toBeTrue()
            ->and($olderEmailAuthentication->verifyCode('123456', $userToAuthenticate))->toBeFalse()
            ->and($newerEmailAuthentication->verifyCode('654321', $userToAuthenticate))->toBeTrue();
    });

    it('can issue and consume an email code when the cache store does not support locks', function (): void {
        Cache::extend('non-locking-email-authentication', fn (): Repository => new Repository(new NonLockingEmailAuthenticationCacheStore));
        config()->set('cache.default', 'non-locking-email-authentication');
        config()->set('cache.stores.non-locking-email-authentication', ['driver' => 'non-locking-email-authentication']);

        $emailAuthentication = EmailAuthentication::make();

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $emailAuthentication->generateCodesUsing(static fn (): string => '123456');

        expect($emailAuthentication->sendCode($userToAuthenticate))->toBeTrue()
            ->and($emailAuthentication->verifyCode('123456', $userToAuthenticate))->toBeTrue()
            ->and($emailAuthentication->verifyCode('123456', $userToAuthenticate))->toBeFalse();
    });

    it('does not send an email code when it cannot be persisted', function (): void {
        Cache::extend('failed-email-authentication-write', fn (): Repository => new Repository(new FailedEmailAuthenticationWriteCacheStore));
        config()->set('cache.default', 'failed-email-authentication-write');
        config()->set('cache.stores.failed-email-authentication-write', ['driver' => 'failed-email-authentication-write']);

        $emailAuthentication = EmailAuthentication::make()
            ->generateCodesUsing(static fn (): string => '123456');

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        expect($emailAuthentication->sendCode($userToAuthenticate))->toBeFalse();

        Notification::assertNothingSent();
    });

    it('does not verify an email code when it cannot be consumed', function (): void {
        Cache::extend('failed-email-authentication-delete', fn (): Repository => new Repository(new FailedEmailAuthenticationDeleteCacheStore));
        config()->set('cache.default', 'failed-email-authentication-delete');
        config()->set('cache.stores.failed-email-authentication-delete', ['driver' => 'failed-email-authentication-delete']);

        $emailAuthentication = EmailAuthentication::make()
            ->generateCodesUsing(static fn (): string => '123456');

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        expect($emailAuthentication->sendCode($userToAuthenticate))->toBeTrue()
            ->and($emailAuthentication->verifyCode('123456', $userToAuthenticate))->toBeFalse();
    });

    it('will not authenticate the user with a challenge code that was issued to a different user', function (): void {
        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $victim = User::factory()->hasEmailAuthentication()->create();
        $attacker = User::factory()->hasEmailAuthentication()->create();

        $issuedCodes = [];

        $emailAuthentication->generateCodesUsing(function () use (&$issuedCodes): string {
            $code = str_pad((string) (count($issuedCodes) + 1), 6, '0', STR_PAD_LEFT);

            $issuedCodes[] = $code;

            return $code;
        });

        $victimLogin = livewire(Login::class)
            ->fillForm([
                'email' => $victim->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null);

        // A second login, in the same session, issues a code to a mailbox that the
        // first user does not control.
        livewire(Login::class)
            ->fillForm([
                'email' => $attacker->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null);

        expect($issuedCodes)->toHaveCount(2);

        $victimLogin
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => $issuedCodes[1],
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate');

        $this->assertGuest();
    });

    it('will not authenticate the user with a challenge code issued to a different user when no code could be sent', function (): void {
        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $victim = User::factory()->hasEmailAuthentication()->create();
        $attacker = User::factory()->hasEmailAuthentication()->create();

        $issuedCodes = [];

        $emailAuthentication->generateCodesUsing(function () use (&$issuedCodes): string {
            $code = str_pad((string) (count($issuedCodes) + 1), 6, '0', STR_PAD_LEFT);

            $issuedCodes[] = $code;

            return $code;
        });

        livewire(Login::class)
            ->fillForm([
                'email' => $attacker->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null);

        // Sending is exhausted for the second user, so reaching their challenge
        // issues no code of its own.
        $victimRateLimitingKey = 'filament-email-authentication:' . Filament::getUserScopedAuthIdentifier($victim);

        RateLimiter::hit($victimRateLimitingKey);
        RateLimiter::hit($victimRateLimitingKey);

        livewire(Login::class)
            ->fillForm([
                'email' => $victim->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => $issuedCodes[0],
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate');

        expect($issuedCodes)->toHaveCount(1);

        $this->assertGuest();
    });

    it('will not authenticate the user through an empty cached challenge schema from a legacy pending payload', function (): void {
        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $code = '123456';
        $emailAuthentication->generateCodesUsing(static fn (): string => $code);

        $login = livewire(EmailAuthenticationLogin::class)
            ->call('setLegacyUserUndertakingMultiFactorAuthenticationForTesting', $userToAuthenticate->getAuthIdentifier())
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ]);

        $login
            ->update(calls: [
                [
                    'method' => 'cacheMultiFactorChallengeFormForTesting',
                    'params' => [],
                    'path' => '',
                ],
                [
                    'method' => 'authenticate',
                    'params' => [],
                    'path' => '',
                ],
                [
                    'method' => 'authenticate',
                    'params' => [],
                    'path' => '',
                ],
            ])
            ->assertHasFormErrors([
                "{$emailAuthentication->getId()}.code" => 'required',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();

        expect(decrypt($login->instance()->userUndertakingMultiFactorAuthentication))
            ->toBe([
                'identifier' => $userToAuthenticate->getAuthIdentifier(),
                'userKey' => Filament::getUserScopedAuthIdentifier($userToAuthenticate),
            ]);

        $login
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => $code,
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoFormErrors(form: 'multiFactorChallengeForm')
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($userToAuthenticate);
    });

    it('will not authenticate the user with a challenge code issued for another guard and model with the same authentication identifier', function (): void {
        Schema::create('email_authentication_users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('has_email_authentication')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        config()->set('auth.providers.email-authentication-users', [
            'driver' => 'eloquent',
            'model' => EmailAuthenticationUser::class,
        ]);
        config()->set('auth.guards.email-authentication-users', [
            'driver' => 'session',
            'provider' => 'email-authentication-users',
        ]);

        $panel = Panel::make()
            ->id('email-authentication-users')
            ->path('email-authentication-users')
            ->login()
            ->authGuard('email-authentication-users')
            ->multiFactorAuthentication(EmailAuthentication::make())
            ->resources([])
            ->pages([]);

        Filament::registerPanel($panel);

        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $attacker = User::factory()
            ->hasEmailAuthentication()
            ->create(['email' => 'attacker@example.test']);

        $victim = EmailAuthenticationUser::query()->create([
            'id' => $attacker->getAuthIdentifier(),
            'name' => 'Victim',
            'email' => 'victim@example.test',
            'password' => Hash::make('victim-password'),
            'has_email_authentication' => true,
        ]);

        $issuedCodes = [];

        $emailAuthentication->generateCodesUsing(function () use (&$issuedCodes): string {
            $code = str_pad((string) (count($issuedCodes) + 1), 6, '0', STR_PAD_LEFT);

            $issuedCodes[] = $code;

            return $code;
        });

        $attackerLogin = livewire(EmailAuthenticationLogin::class)
            ->fillForm([
                'email' => $attacker->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null);

        expect($attackerLogin->instance()->getUserUndertakingMultiFactorAuthenticationForTesting()?->is($attacker))->toBeTrue();

        $attackerLogin->callAction(
            TestAction::make('resend')
                ->schemaComponent("{$emailAuthentication->getId()}.code", schema: 'multiFactorChallengeForm')
        );

        expect($issuedCodes)->toBe(['000001', '000002']);

        Filament::setCurrentPanel($panel);

        expect($attackerLogin->instance()->getUserUndertakingMultiFactorAuthenticationForTesting())->toBeNull();

        /** @var EmailAuthentication $victimEmailAuthentication */
        $victimEmailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());
        $victimEmailAuthentication->generateCodesUsing(function () use (&$issuedCodes): string {
            $code = str_pad((string) (count($issuedCodes) + 1), 6, '0', STR_PAD_LEFT);

            $issuedCodes[] = $code;

            return $code;
        });

        $attackerLogin->fillForm([
            'email' => $victim->email,
            'password' => 'victim-password',
        ]);

        // Simulate a component lookup followed by two queued authentication calls
        // in one Livewire update. The challenge schema must not remain empty after
        // the pending principal is rebound to the victim.
        $attackerLogin
            ->update(calls: [
                [
                    'method' => 'cacheMultiFactorChallengeFormForTesting',
                    'params' => [],
                    'path' => '',
                ],
                [
                    'method' => 'authenticate',
                    'params' => [],
                    'path' => '',
                ],
                [
                    'method' => 'authenticate',
                    'params' => [],
                    'path' => '',
                ],
            ])
            ->assertHasFormErrors([
                "{$victimEmailAuthentication->getId()}.code" => 'required',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        expect($issuedCodes)->toBe(['000001', '000002', '000003']);

        $attackerLogin
            ->fillForm([
                $victimEmailAuthentication->getId() => [
                    'code' => $issuedCodes[1],
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$victimEmailAuthentication->getId()}.code",
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $attackerLogin
            ->fillForm([
                $victimEmailAuthentication->getId() => [
                    'code' => $issuedCodes[2],
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoFormErrors(form: 'multiFactorChallengeForm')
            ->assertRedirect(Filament::getUrl());

        expect($victimEmailAuthentication->sendCode($victim))->toBeTrue()
            ->and($victimEmailAuthentication->sendCode($attacker))->toBeTrue()
            ->and($issuedCodes)->toBe(['000001', '000002', '000003', '000004', '000005'])
            ->and($victimEmailAuthentication->verifyCode('000002', $attacker))->toBeFalse()
            ->and($victimEmailAuthentication->verifyCode('000005', $victim))->toBeFalse()
            ->and($victimEmailAuthentication->verifyCode('000004', $victim))->toBeTrue()
            ->and($victimEmailAuthentication->verifyCode('000005', $attacker))->toBeTrue();

        Filament::setCurrentPanel('email-authentication');

        // Completing the victim login regenerated the shared session ID, which
        // invalidates codes from challenges started before that authentication.
        expect($emailAuthentication->verifyCode('000002', $attacker))->toBeFalse();

        $this->assertAuthenticatedAs($victim, 'email-authentication-users');
    });
});

describe('validation', function (): void {
    test('challenge codes are required', function (): void {
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => '',
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$emailAuthentication->getId()}.code" => 'required',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    test('challenge codes must be numeric', function (): void {
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => Str::random(6),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$emailAuthentication->getId()}.code" => 'numeric',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    test('challenge codes must be 6 digits', function (): void {
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$emailAuthentication->getId()}.code" => 'digits',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    it('can validate `code` is a string', function (): void {
        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()->hasEmailAuthentication()->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->set("data.multiFactor.{$emailAuthentication->getId()}.code", [])
            ->call('authenticate')
            ->assertHasErrors();

        $this->assertGuest();
    });
});

it('can throttle multi-factor challenge attempts per user', function (): void {
    /** @var EmailAuthentication $emailAuthentication */
    $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasEmailAuthentication()
        ->create();

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $emailAuthentication->generateCodesUsing(fn (): string => $code);

    // Clear the IP-based rate limiter between attempts to isolate the
    // user-based rate limit (simulates an attacker rotating IPs).
    $clearIpRateLimiter = function (): void {
        RateLimiter::clear('livewire-rate-limiter:' . sha1(Login::class . '|authenticate|' . request()->ip()));
    };

    $livewire = livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
        ->assertNoRedirect();

    $invalidCode = ($code === '000000') ? '111111' : '000000';

    foreach (range(1, 5) as $i) {
        $clearIpRateLimiter();

        $livewire
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => $invalidCode,
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertNoRedirect();
    }

    $clearIpRateLimiter();

    // The 6th attempt should be rate limited by user ID, even with the valid code
    $livewire
        ->fillForm([
            $emailAuthentication->getId() => [
                'code' => $code,
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertNotified()
        ->assertNoRedirect();

    $this->assertGuest();

    $clearIpRateLimiter();

    // A different user should not be affected by the first user's rate limit
    $secondUser = User::factory()
        ->hasEmailAuthentication()
        ->create();

    $secondCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $emailAuthentication->generateCodesUsing(fn (): string => $secondCode);

    livewire(Login::class)
        ->fillForm([
            'email' => $secondUser->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
        ->assertNoRedirect()
        ->fillForm([
            $emailAuthentication->getId() => [
                'code' => $secondCode,
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticatedAs($secondUser);
});

/**
 * A cache store that implements `Store` but not `LockProvider`.
 */
class NonLockingEmailAuthenticationCacheStore implements Store
{
    /** @var array<string, mixed> */
    protected array $data = [];

    public function get($key): mixed
    {
        return $this->data[$key] ?? null;
    }

    /**
     * @param  array<string>  $keys
     * @return array<string, mixed>
     */
    public function many(array $keys): array
    {
        return array_map(fn (string $key): mixed => $this->get($key), array_combine($keys, $keys));
    }

    public function put($key, $value, $seconds): bool
    {
        $this->data[$key] = $value;

        return true;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function putMany(array $values, $seconds): bool
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $seconds);
        }

        return true;
    }

    public function increment($key, $value = 1): int
    {
        return $this->data[$key] = ((int) ($this->data[$key] ?? 0)) + $value;
    }

    public function decrement($key, $value = 1): int
    {
        return $this->increment($key, -$value);
    }

    public function forever($key, $value): bool
    {
        return $this->put($key, $value, 0);
    }

    public function forget($key): bool
    {
        unset($this->data[$key]);

        return true;
    }

    public function flush(): bool
    {
        $this->data = [];

        return true;
    }

    public function touch($key, $ttl): bool
    {
        return true;
    }

    public function getPrefix(): string
    {
        return '';
    }
}

class FailedEmailAuthenticationWriteCacheStore extends ArrayStore
{
    public function put($key, $value, $seconds): bool
    {
        return false;
    }
}

class FailedEmailAuthenticationDeleteCacheStore extends ArrayStore
{
    public function forget($key): bool
    {
        return false;
    }
}

class OverlappingEmailAuthenticationCacheStore extends ArrayStore
{
    protected ?Closure $afterLockAcquired = null;

    protected ?Closure $beforeReadReturns = null;

    public function afterLockAcquired(Closure $callback): void
    {
        $this->afterLockAcquired = $callback;
    }

    public function beforeReadReturns(Closure $callback): void
    {
        $this->beforeReadReturns = $callback;
    }

    public function get($key): mixed
    {
        $value = parent::get($key);
        $callback = $this->beforeReadReturns;
        $this->beforeReadReturns = null;
        $callback?->__invoke();

        return $value;
    }

    public function lock($name, $seconds = 0, $owner = null): Lock
    {
        if (! $this->afterLockAcquired) {
            return parent::lock($name, $seconds, $owner);
        }

        $callback = $this->afterLockAcquired;
        $this->afterLockAcquired = null;

        return new OverlappingEmailAuthenticationCacheLock($this, $name, $seconds, $owner, $callback);
    }
}

class OverlappingEmailAuthenticationCacheLock extends ArrayLock
{
    public function __construct(
        ArrayStore $store,
        string $name,
        int $seconds,
        ?string $owner,
        protected ?Closure $afterLockAcquired,
    ) {
        parent::__construct($store, $name, $seconds, $owner);
    }

    public function acquire(): bool
    {
        if (! parent::acquire()) {
            return false;
        }

        $callback = $this->afterLockAcquired;
        $this->afterLockAcquired = null;
        $callback?->__invoke();

        return true;
    }
}

class EmailAuthenticationUser extends User
{
    protected $table = 'email_authentication_users';

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}

class EmailAuthenticationLogin extends Login
{
    public function cacheMultiFactorChallengeFormForTesting(): void
    {
        $this->multiFactorChallengeForm->getComponents();
    }

    public function setLegacyUserUndertakingMultiFactorAuthenticationForTesting(mixed $identifier): void
    {
        $this->userUndertakingMultiFactorAuthentication = encrypt($identifier);
    }

    public function getUserUndertakingMultiFactorAuthenticationForTesting(): ?Authenticatable
    {
        return $this->getUserUndertakingMultiFactorAuthentication();
    }
}
