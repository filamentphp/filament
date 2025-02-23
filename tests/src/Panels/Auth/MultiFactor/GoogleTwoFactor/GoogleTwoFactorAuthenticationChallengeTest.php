<?php

use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel('google-two-factor-authentication');
});

it('can render the challenge form after valid login credentials are successfully used', function (): void {
    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication()
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
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication()
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
            $googleTwoFactorAuthentication->getId() => [
                'code' => $googleTwoFactorAuthentication->getCurrentCode($userToAuthenticate),
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticatedAs($userToAuthenticate);
});

it('will make the recovery code field visible when the user requests it', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication()
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
            "{$googleTwoFactorAuthentication->getId()}.recoveryCode",
            'multiFactorChallengeForm',
            fn (TextInput $field): bool => $field->isHidden(),
        )
        ->callAction(TestAction::make('useRecoveryCode')
            ->schemaComponent("multiFactorChallengeForm.{$googleTwoFactorAuthentication->getId()}.code"))
        ->assertFormFieldExists(
            "{$googleTwoFactorAuthentication->getId()}.recoveryCode",
            'multiFactorChallengeForm',
            fn (TextInput $field): bool => $field->isVisible(),
        );
});

it('will authenticate the user after a valid recovery code is used', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication($recoveryCodes = $googleTwoFactorAuthentication->generateRecoveryCodes())
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
            ->schemaComponent("multiFactorChallengeForm.{$googleTwoFactorAuthentication->getId()}.code"))
        ->fillForm([
            $googleTwoFactorAuthentication->getId() => [
                'recoveryCode' => Arr::random($recoveryCodes),
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticatedAs($userToAuthenticate);
});

it('will not render the challenge form after invalid login credentials are used', function (): void {
    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication()
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
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication()
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
            $googleTwoFactorAuthentication->getId() => [
                'code' => ($googleTwoFactorAuthentication->getCurrentCode($userToAuthenticate) === '000000')
                    ? '111111'
                    : '000000',
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasFormErrors([
            "{$googleTwoFactorAuthentication->getId()}.code",
        ], 'multiFactorChallengeForm')
        ->assertNoRedirect();

    $this->assertGuest();
});

test('challenge codes are required', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication()
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
            $googleTwoFactorAuthentication->getId() => [
                'code' => '',
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasFormErrors([
            "{$googleTwoFactorAuthentication->getId()}.code" => 'required',
        ], 'multiFactorChallengeForm')
        ->assertNoRedirect();

    $this->assertGuest();
});

test('challenge codes must be numeric', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication()
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
            $googleTwoFactorAuthentication->getId() => [
                'code' => Str::random(6),
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasFormErrors([
            "{$googleTwoFactorAuthentication->getId()}.code" => 'numeric',
        ], 'multiFactorChallengeForm')
        ->assertNoRedirect();

    $this->assertGuest();
});

test('challenge codes must be 6 digits', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication()
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
            $googleTwoFactorAuthentication->getId() => [
                'code' => Str::limit($googleTwoFactorAuthentication->getCurrentCode($userToAuthenticate), limit: 5, end: ''),
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasFormErrors([
            "{$googleTwoFactorAuthentication->getId()}.code" => 'digits',
        ], 'multiFactorChallengeForm')
        ->assertNoRedirect();

    $this->assertGuest();
});

it('will not authenticate the user when an invalid recovery code is used', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication()
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
            "{$googleTwoFactorAuthentication->getId()}.recoveryCode",
            'multiFactorChallengeForm',
            fn (TextInput $field): bool => $field->isHidden(),
        )
        ->callAction(TestAction::make('useRecoveryCode')
            ->schemaComponent("multiFactorChallengeForm.{$googleTwoFactorAuthentication->getId()}.code"))
        ->assertFormFieldExists(
            "{$googleTwoFactorAuthentication->getId()}.recoveryCode",
            'multiFactorChallengeForm',
            fn (TextInput $field): bool => $field->isVisible(),
        )
        ->fillForm([
            $googleTwoFactorAuthentication->getId() => [
                'recoveryCode' => 'invalid-recovery-code',
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasFormErrors([
            "{$googleTwoFactorAuthentication->getId()}.recoveryCode",
        ], 'multiFactorChallengeForm')
        ->assertNoRedirect();

    $this->assertGuest();
});

it('will not authenticate the user with a valid recovery code if recovery is disabled', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders())
        ->recoverable(false);

    $userToAuthenticate = User::factory()
        ->hasGoogleTwoFactorAuthentication($recoveryCodes = $googleTwoFactorAuthentication->generateRecoveryCodes())
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
            $googleTwoFactorAuthentication->getId() => [
                'recoveryCode' => Arr::random($recoveryCodes),
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasErrors()
        ->assertNoRedirect();

    $this->assertGuest();
});
