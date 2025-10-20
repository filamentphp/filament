<?php

use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel('app-authentication');

    actingAs(User::factory()->create());
});

it('can generate a secret and recovery codes when the action is mounted', function (): void {
    livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpAppAuthentication')
            ->schemaComponent('app', schema: 'content'))
        ->assertActionMounted(TestAction::make('setUpAppAuthentication')
            ->schemaComponent('app', schema: 'content')
            ->arguments(function (array $actualArguments): bool {
                $encrypted = decrypt($actualArguments['encrypted']);

                if (blank($encrypted['secret'] ?? null)) {
                    return false;
                }

                if (blank($encrypted['recoveryCodes'] ?? null)) {
                    return false;
                }

                if (count($encrypted['recoveryCodes']) !== 8) {
                    return false;
                }

                foreach ($encrypted['recoveryCodes'] as $recoveryCode) {
                    if (! is_string($recoveryCode)) {
                        return false;
                    }

                    if (blank($recoveryCode)) {
                        return false;
                    }
                }

                if (blank($encrypted['userId'] ?? null)) {
                    return false;
                }

                return $encrypted['userId'] === auth()->id();
            }));
});

it('can save the secret and recovery codes to the user when the action is submitted', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();

    expect($user->getAppAuthenticationSecret())
        ->toBeEmpty();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeNull();

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpAppAuthentication')
            ->schemaComponent('app', schema: 'content'));

    $encryptedActionArguments = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted']);
    $secret = $encryptedActionArguments['secret'];
    $recoveryCodes = $encryptedActionArguments['recoveryCodes'];

    $livewire
        ->fillForm(['code' => $appAuthentication->getCurrentCode($user, $secret)])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->toBe($secret);

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);

    foreach ($user->getAppAuthenticationRecoveryCodes() as $hashedRecoveryCode) {
        expect(Hash::check(array_shift($recoveryCodes), $hashedRecoveryCode))
            ->toBeTrue();
    }
});

it('will not set up authentication when an invalid code is used', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();

    expect($user->getAppAuthenticationSecret())
        ->toBeEmpty();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeNull();

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpAppAuthentication')
            ->schemaComponent('app', schema: 'content'));

    $encryptedActionArguments = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted']);
    $secret = $encryptedActionArguments['secret'];

    $livewire
        ->fillForm([
            'code' => ($appAuthentication->getCurrentCode($user, $secret) === '000000') ? '111111' : '000000',
        ])
        ->callMountedAction()
        ->assertHasFormErrors();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();

    expect($user->getAppAuthenticationSecret())
        ->toBeEmpty();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeNull();
});

test('codes are required', function (): void {
    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();

    expect($user->getAppAuthenticationSecret())
        ->toBeEmpty();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeNull();

    livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpAppAuthentication')
            ->schemaComponent('app', schema: 'content'))
        ->fillForm(['code' => ''])
        ->callMountedAction()
        ->assertHasFormErrors([
            'code' => 'required',
        ]);

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();

    expect($user->getAppAuthenticationSecret())
        ->toBeEmpty();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeNull();
});

test('codes must be 6 digits', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();

    expect($user->getAppAuthenticationSecret())
        ->toBeEmpty();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeNull();

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpAppAuthentication')
            ->schemaComponent('app', schema: 'content'));

    $encryptedActionArguments = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted']);
    $secret = $encryptedActionArguments['secret'];

    $livewire
        ->fillForm([
            'code' => Str::limit($appAuthentication->getCurrentCode($user, $secret), limit: 5, end: ''),
        ])
        ->callMountedAction()
        ->assertHasFormErrors([
            'code' => 'digits',
        ]);

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();

    expect($user->getAppAuthenticationSecret())
        ->toBeEmpty();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeNull();
});
