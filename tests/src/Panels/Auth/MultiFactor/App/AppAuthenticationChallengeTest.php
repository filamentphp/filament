<?php

use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Events\Validated;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
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
            ->toBe($userToAuthenticate->getKey());

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

        $this->travelTo(now()->addMinutes(2));
        $futureCode = $appAuthentication->getCurrentCode($userToAuthenticate);
        $this->travelBack();

        expect($appAuthentication->verifyCode($futureCode, $secret, shouldPreventCodeReuse: true))->toBeTrue();
        expect($appAuthentication->verifyCode($futureCode, $secret, shouldPreventCodeReuse: true))->toBeFalse();
    });

    it('can still verify TOTP codes when the cache store does not support locks', function (): void {
        Cache::swap(new Repository(new NonLockingCacheStore));

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
        expect($appAuthentication->verifyCode($currentCode, $secret, shouldPreventCodeReuse: true))->toBeFalse();
        expect($appAuthentication->verifyCode($earlierCode, $secret, shouldPreventCodeReuse: true))->toBeFalse();
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
