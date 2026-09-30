<?php

use Filament\Actions\Testing\TestAction;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Validated;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\DynamoDbStore;
use Illuminate\Cache\FailoverStore;
use Illuminate\Cache\Lock;
use Illuminate\Cache\MemoizedStore;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use PragmaRX\Google2FAQRCode\Google2FA;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel('app-authentication');
});

describe('authentication flow', function (): void {
    it('can render the challenge form after valid login credentials are successfully used', function (): void {
        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        $livewire = livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->assertSet('userUndertakingMultiFactorAuthentication', null)
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect();

        expect(decrypt($livewire->instance()->userUndertakingMultiFactorAuthentication))
            ->toBe([
                'identifier' => $userToAuthenticate->getAuthIdentifier(),
                'userKey' => Filament::getUserScopedAuthIdentifier($userToAuthenticate),
            ]);

        $this->assertGuest();
    });

    it('will authenticate the user after a valid challenge code is used', function (): void {
        Event::fake([Validated::class]);

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        $livewire = livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect();

        Event::assertNotDispatched(Validated::class);

        $livewire
            ->fillForm([
                $appAuthentication->getId() => [
                    'code' => $appAuthentication->getCurrentCode($userToAuthenticate),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($userToAuthenticate);

        Event::assertDispatchedTimes(Validated::class, 1);
    });

    it('will make the recovery code field visible when the user requests it', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->assertFormFieldExists(
                "{$appAuthentication->getId()}.recoveryCode",
                'multiFactorChallengeForm',
                fn (TextInput $field): bool => $field->isHidden(),
            )
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent("{$appAuthentication->getId()}.code", schema: 'multiFactorChallengeForm'))
            ->assertFormFieldExists(
                "{$appAuthentication->getId()}.recoveryCode",
                'multiFactorChallengeForm',
                fn (TextInput $field): bool => $field->isVisible(),
            );
    });

    it('will authenticate the user after a valid recovery code is used', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($recoveryCodes = $appAuthentication->generateRecoveryCodes())
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent("{$appAuthentication->getId()}.code", schema: 'multiFactorChallengeForm'))
            ->fillForm([
                $appAuthentication->getId() => [
                    'recoveryCode' => Arr::random($recoveryCodes),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($userToAuthenticate);
    });

    it('does not authenticate when the password changes during the multi-factor challenge', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($recoveryCodes = $appAuthentication->generateRecoveryCodes())
            ->create([
                'password' => Hash::make('password', ['rounds' => 4]),
            ]);

        config()->set('hashing.bcrypt.rounds', 5);
        Hash::forgetDrivers();

        $livewire = livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent("{$appAuthentication->getId()}.code", schema: 'multiFactorChallengeForm'));

        $failedEvents = 0;
        $passwordWasReset = false;
        $userClass = $userToAuthenticate::class;

        Event::listen(Failed::class, static function () use (&$failedEvents): void {
            $failedEvents++;
        });

        Event::listen("eloquent.saved: {$userClass}", static function (User $savedUser) use (&$passwordWasReset, $userToAuthenticate): void {
            if ($passwordWasReset || ($savedUser->getKey() !== $userToAuthenticate->getKey())) {
                return;
            }

            $passwordWasReset = true;

            $savedUser->newQuery()
                ->whereKey($savedUser->getKey())
                ->update(['password' => Hash::make('new-password')]);
        });

        $livewire
            ->fillForm([
                $appAuthentication->getId() => [
                    'recoveryCode' => Arr::random($recoveryCodes),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors(['email'])
            ->assertNoRedirect();

        $this->assertGuest();

        $freshUser = $userToAuthenticate->fresh();

        expect(Hash::check('new-password', $freshUser->password))->toBeTrue()
            ->and(Hash::check('password', $freshUser->password))->toBeFalse()
            ->and($failedEvents)->toBe(1);
    });

    it('will authenticate the user with a one-time code after enabling the recovery code field', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($appAuthentication->generateRecoveryCodes())
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent("{$appAuthentication->getId()}.code", schema: 'multiFactorChallengeForm'))
            // Having enabled the recovery code field, the user can still change their mind and authenticate
            // with their one-time code, leaving the recovery code blank.
            ->fillForm([
                $appAuthentication->getId() => [
                    'code' => $appAuthentication->getCurrentCode($userToAuthenticate),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($userToAuthenticate);
    });
});

describe('failure cases', function (): void {
    it('will not render the challenge form after invalid login credentials are used', function (): void {
        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
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
    });

    it('will not authenticate the user when an invalid challenge code is used', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
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
                $appAuthentication->getId() => [
                    'code' => ($appAuthentication->getCurrentCode($userToAuthenticate) === '000000')
                        ? '111111'
                        : '000000',
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.code",
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });
});

describe('validation', function (): void {
    test('a one-time code is still required when the recovery code field is enabled but left blank', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($appAuthentication->generateRecoveryCodes())
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent("{$appAuthentication->getId()}.code", schema: 'multiFactorChallengeForm'))
            // Enabling the recovery code field does not force the user down the recovery path: with the
            // recovery code left blank the one-time code is still required, so it can be used instead.
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.code" => 'required',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    test('a one-time code is still required when the recovery code field is enabled directly through the form state but left blank', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($appAuthentication->generateRecoveryCodes())
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            // Enabling the recovery code field by writing directly to the state, rather than through the
            // action, must still leave the one-time code required while the recovery code is blank.
            ->set("data.multiFactor.{$appAuthentication->getId()}.useRecoveryCode", true)
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.code" => 'required',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    test('challenge codes are required', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
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
                $appAuthentication->getId() => [
                    'code' => '',
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.code" => 'required',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    test('challenge codes must be numeric', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
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
                $appAuthentication->getId() => [
                    'code' => Str::random(6),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.code" => 'numeric',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    test('challenge codes must be 6 digits', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
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
                $appAuthentication->getId() => [
                    'code' => Str::limit($appAuthentication->getCurrentCode($userToAuthenticate), limit: 5, end: ''),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.code" => 'digits',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    it('can validate `code` is a string', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->set("data.multiFactor.{$appAuthentication->getId()}.code", [])
            ->call('authenticate')
            ->assertHasErrors();

        $this->assertGuest();
    });
});

describe('recovery codes', function (): void {
    it('will not authenticate the user when a recovery code is submitted without enabling the recovery code field', function (string $recoveryCode): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            // The recovery code field is hidden until the user chooses to use a recovery code, so a value
            // present in the field while it is hidden must be ignored and the one-time code remains required.
            ->assertFormFieldExists(
                "{$appAuthentication->getId()}.recoveryCode",
                'multiFactorChallengeForm',
                fn (TextInput $field): bool => $field->isHidden(),
            )
            ->set("data.multiFactor.{$appAuthentication->getId()}.recoveryCode", $recoveryCode)
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.code" => 'required',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    })->with([
        'an arbitrary string' => 'invalid-recovery-code',
        'a value that is not blank but resembles a falsy value' => '0',
        'a single character' => 'x',
    ]);

    it('will not authenticate the user with a valid recovery code that is submitted without enabling the recovery code field', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($recoveryCodes = $appAuthentication->generateRecoveryCodes())
            ->create();

        $recoveryCodeCount = count($userToAuthenticate->app_authentication_recovery_codes);

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            // A valid recovery code should only be honored once the user has enabled the recovery code
            // field; while it is hidden the code is neither validated nor consumed.
            ->set("data.multiFactor.{$appAuthentication->getId()}.recoveryCode", Arr::random($recoveryCodes))
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.code" => 'required',
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();

        expect($userToAuthenticate->refresh()->app_authentication_recovery_codes)
            ->toHaveCount($recoveryCodeCount);
    });

    it('will not authenticate the user when an invalid recovery code is used', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->assertFormFieldExists(
                "{$appAuthentication->getId()}.recoveryCode",
                'multiFactorChallengeForm',
                fn (TextInput $field): bool => $field->isHidden(),
            )
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent("{$appAuthentication->getId()}.code", schema: 'multiFactorChallengeForm'))
            ->assertFormFieldExists(
                "{$appAuthentication->getId()}.recoveryCode",
                'multiFactorChallengeForm',
                fn (TextInput $field): bool => $field->isVisible(),
            )
            ->fillForm([
                $appAuthentication->getId() => [
                    'recoveryCode' => 'invalid-recovery-code',
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.recoveryCode",
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    it('will not authenticate the user with a valid recovery code if recovery is disabled', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders())
            ->recoverable(false);

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($recoveryCodes = $appAuthentication->generateRecoveryCodes())
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
                $appAuthentication->getId() => [
                    'recoveryCode' => Arr::random($recoveryCodes),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest();
    });

    it('will not allow a recovery code to be used more than once', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($recoveryCodes = $appAuthentication->generateRecoveryCodes())
            ->create();

        $recoveryCodeToUse = Arr::first($recoveryCodes);

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent("{$appAuthentication->getId()}.code", schema: 'multiFactorChallengeForm'))
            ->fillForm([
                $appAuthentication->getId() => [
                    'recoveryCode' => $recoveryCodeToUse,
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($userToAuthenticate);

        auth()->logout();

        $this->assertGuest();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent("{$appAuthentication->getId()}.code", schema: 'multiFactorChallengeForm'))
            ->fillForm([
                $appAuthentication->getId() => [
                    'recoveryCode' => $recoveryCodeToUse,
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.recoveryCode",
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    it('will not preserve a recovery code when a different code is used concurrently', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($recoveryCodes = $appAuthentication->generateRecoveryCodes())
            ->create();

        $initialCount = count($userToAuthenticate->app_authentication_recovery_codes);

        $userInstanceA = User::find($userToAuthenticate->getKey());
        $userInstanceB = User::find($userToAuthenticate->getKey());

        expect($appAuthentication->verifyRecoveryCode($recoveryCodes[0], $userInstanceA))->toBeTrue();
        expect($appAuthentication->verifyRecoveryCode($recoveryCodes[1], $userInstanceB))->toBeTrue();

        $userToAuthenticate->refresh();

        expect($userToAuthenticate->app_authentication_recovery_codes)->toHaveCount($initialCount - 2);

        expect($appAuthentication->verifyRecoveryCode($recoveryCodes[0], $userToAuthenticate->fresh()))->toBeFalse();
    });

    it('will not allow the same recovery code to authenticate two concurrent requests', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($recoveryCodes = $appAuthentication->generateRecoveryCodes())
            ->create();

        $userInstanceA = User::find($userToAuthenticate->getKey());
        $userInstanceB = User::find($userToAuthenticate->getKey());

        $code = $recoveryCodes[0];

        expect($appAuthentication->verifyRecoveryCode($code, $userInstanceA))->toBeTrue();
        expect($appAuthentication->verifyRecoveryCode($code, $userInstanceB))->toBeFalse();
    });
});

describe('security', function (): void {
    it('can throttle multi-factor challenge attempts per user', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

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

        $invalidCode = ($appAuthentication->getCurrentCode($userToAuthenticate) === '000000')
            ? '111111'
            : '000000';

        foreach (range(1, 5) as $i) {
            $clearIpRateLimiter();

            $livewire
                ->fillForm([
                    $appAuthentication->getId() => [
                        'code' => $invalidCode,
                    ],
                ], 'multiFactorChallengeForm')
                ->call('authenticate')
                ->assertNoRedirect();
        }

        $clearIpRateLimiter();

        // The 6th attempt should be rate limited by user ID, even with a valid code
        $livewire
            ->fillForm([
                $appAuthentication->getId() => [
                    'code' => $appAuthentication->getCurrentCode($userToAuthenticate),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertNotified()
            ->assertNoRedirect();

        $this->assertGuest();

        $clearIpRateLimiter();

        // A different user should not be affected by the first user's rate limit
        $secondUser = User::factory()
            ->hasAppAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $secondUser->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->fillForm([
                $appAuthentication->getId() => [
                    'code' => $appAuthentication->getCurrentCode($secondUser),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($secondUser);
    });

    it('will not allow a TOTP code to be reused within the same time window', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        $validCode = $appAuthentication->getCurrentCode($userToAuthenticate);

        // First login with the TOTP code should succeed
        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->fillForm([
                $appAuthentication->getId() => [
                    'code' => $validCode,
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($userToAuthenticate);

        auth()->logout();

        $this->assertGuest();

        // Second login with the same TOTP code should fail (replay protection)
        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->fillForm([
                $appAuthentication->getId() => [
                    'code' => $validCode,
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasFormErrors([
                "{$appAuthentication->getId()}.code",
            ], 'multiFactorChallengeForm')
            ->assertNoRedirect();

        $this->assertGuest();
    });

    it('will not allow a TOTP code from an earlier time window to be used after a newer code', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        $secret = $appAuthentication->getSecret($userToAuthenticate);

        $google2FA = app(Google2FA::class);

        $timestamp = $google2FA->getTimestamp();
        $earlierCode = $google2FA->oathTotp($secret, $timestamp - 4);
        $currentCode = $google2FA->oathTotp($secret, $timestamp);

        expect($appAuthentication->verifyCode($currentCode, $secret, shouldPreventCodeReuse: true))->toBeTrue();
        expect($appAuthentication->verifyCode($earlierCode, $secret, shouldPreventCodeReuse: true))->toBeFalse();
    });

    it('will not allow a TOTP code from a future time window to be reused', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        $secret = $appAuthentication->getSecret($userToAuthenticate);

        $google2FA = app(Google2FA::class);

        $futureCode = $google2FA->oathTotp($secret, $google2FA->getTimestamp() + 1);

        expect($appAuthentication->verifyCode($futureCode, $secret, shouldPreventCodeReuse: true))->toBeTrue();
        expect($appAuthentication->verifyCode($futureCode, $secret, shouldPreventCodeReuse: true))->toBeFalse();
    });

    it('cannot prevent TOTP code reuse when the cache store does not support locks', function (): void {
        Cache::extend('non-locking', fn (): Repository => new Repository(new NonLockingCacheStore));
        config()->set('cache.stores.non-locking', ['driver' => 'non-locking']);
        config()->set('cache.default', 'non-locking');

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        $secret = $appAuthentication->getSecret($userToAuthenticate);

        expect(fn (): bool => $appAuthentication->verifyCode(
            $appAuthentication->getCurrentCode($userToAuthenticate),
            $secret,
            shouldPreventCodeReuse: true,
        ))->toThrow(LogicException::class, 'The [non-locking] cache store must support atomic locks to use multi-factor authentication.');
    });

    it('cannot use the `array` cache store outside unit tests', function (): void {
        app()->detectEnvironment(static fn (): string => 'production');

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        expect(fn (): bool => $appAuthentication->verifyCode(
            $appAuthentication->getCurrentCode($userToAuthenticate),
            $appAuthentication->getSecret($userToAuthenticate),
            shouldPreventCodeReuse: true,
        ))->toThrow(LogicException::class, 'The array cache store is not shared between processes and cannot be used for multi-factor authentication.');

        app()->detectEnvironment(static fn (): string => 'testing');
    });

    it('cannot use the DynamoDB cache store', function (): void {
        $dynamoDbStore = (new ReflectionClass(DynamoDbStore::class))->newInstanceWithoutConstructor();

        Cache::extend('dynamodb-test', fn (): Repository => new Repository($dynamoDbStore));
        config()->set('cache.default', 'dynamodb-test');
        config()->set('cache.stores.dynamodb-test', ['driver' => 'dynamodb-test']);

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        expect(fn (): bool => $appAuthentication->verifyCode(
            $appAuthentication->getCurrentCode($userToAuthenticate),
            $appAuthentication->getSecret($userToAuthenticate),
            shouldPreventCodeReuse: true,
        ))->toThrow(LogicException::class, 'The DynamoDB cache store does not provide the consistent reads required to use multi-factor authentication.');
    });

    it('cannot use the failover cache store', function (): void {
        if (! class_exists(FailoverStore::class)) {
            $this->markTestSkipped();
        }

        $failoverStore = (new ReflectionClass(FailoverStore::class))->newInstanceWithoutConstructor();

        Cache::extend('failover-test', fn (): Repository => new Repository($failoverStore));
        config()->set('cache.default', 'failover-test');
        config()->set('cache.stores.failover-test', ['driver' => 'failover-test']);

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        expect(fn (): bool => $appAuthentication->verifyCode(
            $appAuthentication->getCurrentCode($userToAuthenticate),
            $appAuthentication->getSecret($userToAuthenticate),
            shouldPreventCodeReuse: true,
        ))->toThrow(LogicException::class, 'The failover cache store cannot provide one authoritative store for multi-factor authentication.');
    });

    it('cannot use the memoized cache store', function (): void {
        if (! class_exists(MemoizedStore::class)) {
            $this->markTestSkipped();
        }

        $memoizedStore = (new ReflectionClass(MemoizedStore::class))->newInstanceWithoutConstructor();

        Cache::extend('memoized-test', fn (): Repository => new Repository($memoizedStore));
        config()->set('cache.default', 'memoized-test');
        config()->set('cache.stores.memoized-test', ['driver' => 'memoized-test']);

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        expect(fn (): bool => $appAuthentication->verifyCode(
            $appAuthentication->getCurrentCode($userToAuthenticate),
            $appAuthentication->getSecret($userToAuthenticate),
            shouldPreventCodeReuse: true,
        ))->toThrow(LogicException::class, 'The memoized cache store cannot provide authoritative reads for multi-factor authentication.');
    });

    it('can use a different cache store to prevent TOTP code reuse', function (): void {
        Cache::extend('non-locking', fn (): Repository => new Repository(new NonLockingCacheStore));
        config()->set('cache.stores.non-locking', ['driver' => 'non-locking']);
        config()->set('cache.default', 'non-locking');
        config()->set('cache.stores.mfa', ['driver' => 'array']);

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());
        $appAuthentication->cacheStore('mfa');

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        $secret = $appAuthentication->getSecret($userToAuthenticate);
        $currentCode = $appAuthentication->getCurrentCode($userToAuthenticate);

        expect($appAuthentication->getCacheStore())->toBe('mfa')
            ->and($appAuthentication->verifyCode($currentCode, $secret, shouldPreventCodeReuse: true))->toBeTrue()
            ->and($appAuthentication->verifyCode($currentCode, $secret, shouldPreventCodeReuse: true))->toBeFalse();
    });

    it('cannot use the `null` cache store for TOTP replay protection', function (): void {
        config()->set('cache.default', 'null');
        config()->set('cache.stores.null', ['driver' => 'null']);

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        expect(fn (): bool => $appAuthentication->verifyCode(
            $appAuthentication->getCurrentCode($userToAuthenticate),
            $appAuthentication->getSecret($userToAuthenticate),
            shouldPreventCodeReuse: true,
        ))->toThrow(LogicException::class, 'The [null] cache store must support atomic locks to use multi-factor authentication.');
    });

    it('cannot use the `null` cache store to protect recovery codes', function (): void {
        config()->set('cache.default', 'null');
        config()->set('cache.stores.null', ['driver' => 'null']);

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication($recoveryCodes = $appAuthentication->generateRecoveryCodes())
            ->create();

        expect(fn (): bool => $appAuthentication->verifyRecoveryCode(
            Arr::first($recoveryCodes),
            $userToAuthenticate,
        ))->toThrow(LogicException::class, 'The [null] cache store must support atomic locks to use multi-factor authentication.');
    });

    it('does not verify a TOTP code when the replay watermark cannot be persisted', function (): void {
        Cache::extend('failed-write', fn (): Repository => new Repository(new FailedWriteCacheStore));
        config()->set('cache.default', 'failed-write');
        config()->set('cache.stores.failed-write', ['driver' => 'failed-write']);

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        expect($appAuthentication->verifyCode(
            $appAuthentication->getCurrentCode($userToAuthenticate),
            $appAuthentication->getSecret($userToAuthenticate),
            shouldPreventCodeReuse: true,
        ))->toBeFalse();
    });

    it('does not verify a TOTP code when cache lock ownership is lost', function (): void {
        Cache::extend('lost-lock-ownership', fn (): Repository => new Repository(new LostLockOwnershipCacheStore));
        config()->set('cache.default', 'lost-lock-ownership');
        config()->set('cache.stores.lost-lock-ownership', ['driver' => 'lost-lock-ownership']);

        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        expect($appAuthentication->verifyCode(
            $appAuthentication->getCurrentCode($userToAuthenticate),
            $appAuthentication->getSecret($userToAuthenticate),
            shouldPreventCodeReuse: true,
        ))->toBeFalse();
    });

    it('will not allow a TOTP code accepted with a smaller `codeWindow()` to be replayed by a larger `codeWindow()`', function (): void {
        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        $shortWindowAppAuthentication = AppAuthentication::make()->codeWindow(0);
        $largeWindowAppAuthentication = AppAuthentication::make()->codeWindow(8);
        $secret = $shortWindowAppAuthentication->getSecret($userToAuthenticate);
        $code = $shortWindowAppAuthentication->getCurrentCode($userToAuthenticate);

        expect($shortWindowAppAuthentication->verifyCode($code, $secret, shouldPreventCodeReuse: true))->toBeTrue();

        $this->travel(61)->seconds();

        expect($largeWindowAppAuthentication->verifyCode($code, $secret, shouldPreventCodeReuse: true))->toBeFalse();
    });
});

/**
 * A cache store that implements `Store` but not `LockProvider`, like `ApcStore`,
 * `StorageStore`, `SessionStore` and many third-party cache drivers.
 */
class NonLockingCacheStore implements Store
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

class LostLockOwnershipCacheStore extends ArrayStore
{
    public function lock($name, $seconds = 0, $owner = null): Lock
    {
        return new LostLockOwnershipCacheLock($name, $seconds, $owner);
    }
}

class FailedWriteCacheStore extends ArrayStore
{
    public function forever($key, $value): bool
    {
        return false;
    }
}

class LostLockOwnershipCacheLock extends Lock
{
    public function acquire(): bool
    {
        return true;
    }

    public function release(): bool
    {
        return true;
    }

    protected function getCurrentOwner(): ?string
    {
        return 'different-owner';
    }

    public function forceRelease(): void {}
}
