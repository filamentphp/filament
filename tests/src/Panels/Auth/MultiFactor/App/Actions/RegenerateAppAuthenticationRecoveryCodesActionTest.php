<?php

use Filament\Actions\Testing\TestAction;
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
    Filament::setCurrentPanel('app-authentication');

    actingAs(User::factory()
        ->hasAppAuthentication()
        ->create());
});

it('can generate new recovery codes when valid challenge code is used', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    $recoveryCodes = $user->getAppAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            ['code' => $appAuthentication->getCurrentCode($user)],
        )
        ->assertHasNoFormErrors()
        ->assertActionMounted([
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
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

    expect($user->getAppAuthenticationRecoveryCodes())
        ->not()->toBe($recoveryCodes)
        ->toBeArray()
        ->toHaveCount(8);
});

it('can generate new recovery codes when the current user\'s password is used', function (): void {
    $user = auth()->user();

    $recoveryCodes = $user->getAppAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            ['password' => 'password'],
        )
        ->assertHasNoFormErrors()
        ->assertActionMounted([
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
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

    expect($user->getAppAuthenticationRecoveryCodes())
        ->not()->toBe($recoveryCodes)
        ->toBeArray()
        ->toHaveCount(8);
});

it('will not generate new recovery codes when an invalid code is used', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    $recoveryCodes = $user->getAppAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            ['code' => ($appAuthentication->getCurrentCode($user) === '000000') ? '111111' : '000000'],
        )
        ->assertHasFormErrors()
        ->assertActionNotMounted([
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            TestAction::make('showNewRecoveryCodes'),
        ]);

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBe($recoveryCodes);
});

test('codes are required without the user\'s current password', function (): void {
    $user = auth()->user();

    $recoveryCodes = $user->getAppAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            ['code' => ''],
        )
        ->assertHasFormErrors([
            'code' => 'required_without',
        ])
        ->assertActionNotMounted([
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            TestAction::make('showNewRecoveryCodes'),
        ]);

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBe($recoveryCodes);
});

test('codes must be 6 digits', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    $recoveryCodes = $user->getAppAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            ['code' => Str::limit($appAuthentication->getCurrentCode($user), limit: 5, end: '')],
        )
        ->assertHasFormErrors([
            'code' => 'digits',
        ])
        ->assertActionNotMounted([
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            TestAction::make('showNewRecoveryCodes'),
        ]);

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBe($recoveryCodes);
});

test('the user\'s current password must be valid', function (): void {
    $user = auth()->user();

    $recoveryCodes = $user->getAppAuthenticationRecoveryCodes();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            ['password' => 'incorrect-password'],
        )
        ->assertHasFormErrors([
            'password' => 'current_password',
        ])
        ->assertActionNotMounted([
            TestAction::make('regenerateAppAuthenticationRecoveryCodes')
                ->schemaComponent('app', schema: 'content'),
            TestAction::make('showNewRecoveryCodes'),
        ]);

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBe($recoveryCodes);
});
