<?php

use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Auth\MultiFactor\EmailCode\Notifications\VerifyEmailCodeAuthentication;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel('email-code-authentication');

    Notification::fake();
});

it('can render the challenge form after valid login credentials are successfully used', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasEmailCodeAuthentication()
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

    Notification::assertSentTo($userToAuthenticate, VerifyEmailCodeAuthentication::class, function (VerifyEmailCodeAuthentication $notification) use ($emailCodeAuthentication, $userToAuthenticate): bool {
        if ($notification->codeWindow !== $emailCodeAuthentication->getCodeWindow()) {
            return false;
        }

        return $notification->code === $emailCodeAuthentication->getCurrentCode($userToAuthenticate);
    });
});

it('will authenticate the user after a valid challenge code is used', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasEmailCodeAuthentication()
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
            $emailCodeAuthentication->getId() => [
                'code' => $emailCodeAuthentication->getCurrentCode($userToAuthenticate),
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticatedAs($userToAuthenticate);
});

it('can resend the code to the user', function (): void {
    $this->travelTo(now()->subMinute());

    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasEmailCodeAuthentication()
        ->create();

    $livewire = livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ])
        ->call('authenticate');

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 1);

    $this->travelBack();

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent("multiFactorChallengeForm.{$emailCodeAuthentication->getId()}.code"));

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 2);
});

it('can not resend the code to the user more than once per minute', function (): void {
    $this->travelTo(now()->subMinute());

    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasEmailCodeAuthentication()
        ->create();

    $livewire = livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ])
        ->call('authenticate');

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 1);

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent("multiFactorChallengeForm.{$emailCodeAuthentication->getId()}.code"));

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 1);

    $this->travelBack();

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent("multiFactorChallengeForm.{$emailCodeAuthentication->getId()}.code"));

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 2);
});

it('will not render the challenge form after invalid login credentials are used', function (): void {
    $userToAuthenticate = User::factory()
        ->hasEmailCodeAuthentication()
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

    Notification::assertNotSentTo($userToAuthenticate, VerifyEmailCodeAuthentication::class);
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

    Notification::assertNotSentTo($userToAuthenticate, VerifyEmailCodeAuthentication::class);
});

it('will not authenticate the user when an invalid challenge code is used', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasEmailCodeAuthentication()
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
            $emailCodeAuthentication->getId() => [
                'code' => ($emailCodeAuthentication->getCurrentCode($userToAuthenticate) === '000000')
                    ? '111111'
                    : '000000',
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasFormErrors([
            "{$emailCodeAuthentication->getId()}.code",
        ], 'multiFactorChallengeForm')
        ->assertNoRedirect();

    $this->assertGuest();
});

test('challenge codes are required', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasEmailCodeAuthentication()
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
            $emailCodeAuthentication->getId() => [
                'code' => '',
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasFormErrors([
            "{$emailCodeAuthentication->getId()}.code" => 'required',
        ], 'multiFactorChallengeForm')
        ->assertNoRedirect();

    $this->assertGuest();
});

test('challenge codes must be numeric', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasEmailCodeAuthentication()
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
            $emailCodeAuthentication->getId() => [
                'code' => Str::random(6),
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasFormErrors([
            "{$emailCodeAuthentication->getId()}.code" => 'numeric',
        ], 'multiFactorChallengeForm')
        ->assertNoRedirect();

    $this->assertGuest();
});

test('challenge codes must be 6 digits', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $userToAuthenticate = User::factory()
        ->hasEmailCodeAuthentication()
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
            $emailCodeAuthentication->getId() => [
                'code' => Str::limit($emailCodeAuthentication->getCurrentCode($userToAuthenticate), limit: 5, end: ''),
            ],
        ], 'multiFactorChallengeForm')
        ->call('authenticate')
        ->assertHasFormErrors([
            "{$emailCodeAuthentication->getId()}.code" => 'digits',
        ], 'multiFactorChallengeForm')
        ->assertNoRedirect();

    $this->assertGuest();
});
