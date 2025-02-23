<?php

use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel('google-two-factor-authentication');

    actingAs(User::factory()
        ->hasGoogleTwoFactorAuthentication()
        ->create());
});

it('can generate new recovery codes when valid challenge code is used', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    $recoveryCodes = $user->getGoogleTwoFactorAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            ['code' => $googleTwoFactorAuthentication->getCurrentCode($user)],
        )
        ->assertHasNoActionErrors()
        ->assertActionMounted([
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            TestAction::make('showNewRecoveryCodes')
                ->arguments(function (array $actualArguments): bool {
                    if (blank($actualArguments['recoveryCodes'] ?? null)) {
                        return false;
                    }

                    if (count($actualArguments['recoveryCodes']) !== 8) {
                        return false;
                    }

                    foreach ($actualArguments['recoveryCodes'] as $recoveryCode) {
                        if (! is_string($recoveryCode)) {
                            return false;
                        }

                        if (blank($recoveryCode)) {
                            return false;
                        }
                    }

                    return true;
                }),
        ]);

    expect($user->getGoogleTwoFactorAuthenticationRecoveryCodes())
        ->not()->toBe($recoveryCodes)
        ->toBeArray()
        ->toHaveCount(8);
});

it('can generate new recovery codes when the current user\'s password is used', function (): void {
    $user = auth()->user();

    $recoveryCodes = $user->getGoogleTwoFactorAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            ['password' => 'password'],
        )
        ->assertHasNoActionErrors()
        ->assertActionMounted([
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            TestAction::make('showNewRecoveryCodes')
                ->arguments(function (array $actualArguments): bool {
                    if (blank($actualArguments['recoveryCodes'] ?? null)) {
                        return false;
                    }

                    if (count($actualArguments['recoveryCodes']) !== 8) {
                        return false;
                    }

                    foreach ($actualArguments['recoveryCodes'] as $recoveryCode) {
                        if (! is_string($recoveryCode)) {
                            return false;
                        }

                        if (blank($recoveryCode)) {
                            return false;
                        }
                    }

                    return true;
                }),
        ]);

    expect($user->getGoogleTwoFactorAuthenticationRecoveryCodes())
        ->not()->toBe($recoveryCodes)
        ->toBeArray()
        ->toHaveCount(8);
});

it('will not generate new recovery codes when an invalid code is used', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    $recoveryCodes = $user->getGoogleTwoFactorAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            ['code' => ($googleTwoFactorAuthentication->getCurrentCode($user) === '000000') ? '111111' : '000000'],
        )
        ->assertHasActionErrors()
        ->assertActionNotMounted([
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            TestAction::make('showNewRecoveryCodes'),
        ]);

    expect($user->getGoogleTwoFactorAuthenticationRecoveryCodes())
        ->toBe($recoveryCodes);
});

test('codes are required without the user\'s current password', function (): void {
    $user = auth()->user();

    $recoveryCodes = $user->getGoogleTwoFactorAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            ['code' => ''],
        )
        ->assertHasActionErrors([
            'code' => 'required_without',
        ])
        ->assertActionNotMounted([
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            TestAction::make('showNewRecoveryCodes'),
        ]);

    expect($user->getGoogleTwoFactorAuthenticationRecoveryCodes())
        ->toBe($recoveryCodes);
});

test('codes must be 6 digits', function (): void {
    $googleTwoFactorAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    $recoveryCodes = $user->getGoogleTwoFactorAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            ['code' => Str::limit($googleTwoFactorAuthentication->getCurrentCode($user), limit: 5, end: '')],
        )
        ->assertHasActionErrors([
            'code' => 'digits',
        ])
        ->assertActionNotMounted([
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            TestAction::make('showNewRecoveryCodes'),
        ]);

    expect($user->getGoogleTwoFactorAuthenticationRecoveryCodes())
        ->toBe($recoveryCodes);
});

test('the user\'s current password must be valid', function (): void {
    $user = auth()->user();

    $recoveryCodes = $user->getGoogleTwoFactorAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            ['password' => 'incorrect-password'],
        )
        ->assertHasActionErrors([
            'password' => 'current_password',
        ])
        ->assertActionNotMounted([
            TestAction::make('regenerateGoogleTwoFactorAuthenticationRecoveryCodes')
                ->schemaComponent('content.google_two_factor'),
            TestAction::make('showNewRecoveryCodes'),
        ]);

    expect($user->getGoogleTwoFactorAuthenticationRecoveryCodes())
        ->toBe($recoveryCodes);
});
