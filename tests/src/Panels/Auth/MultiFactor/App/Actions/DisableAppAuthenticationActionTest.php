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

    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $this->recoveryCodes = $appAuthentication->generateRecoveryCodes();

    actingAs(User::factory()
        ->hasAppAuthentication($this->recoveryCodes)
        ->create());
});

it('can disable authentication when valid challenge code is used', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('disableAppAuthentication')
                ->schemaComponent('app', schema: 'content'),
            ['code' => $appAuthentication->getCurrentCode($user)],
        )
        ->assertHasNoFormErrors();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();

    expect($user->getAppAuthenticationSecret())
        ->toBeEmpty();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeNull();
});

it('can disable authentication when a valid recovery code is used', function (): void {
    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);

    livewire(EditProfile::class)
        ->mountAction(TestAction::make('disableAppAuthentication')
            ->schemaComponent('app', schema: 'content'))
        ->callAction(TestAction::make('useRecoveryCode')
            ->schemaComponent('code'))
        ->fillForm([
            'recoveryCode' => Arr::first($this->recoveryCodes),
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();

    expect($user->getAppAuthenticationSecret())
        ->toBeEmpty();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeNull();
});

it('will not disable authentication when an invalid code is used', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('disableAppAuthentication')
                ->schemaComponent('app', schema: 'content'),
            ['code' => ($appAuthentication->getCurrentCode($user) === '000000') ? '111111' : '000000'],
        )
        ->assertHasFormErrors();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);
});

test('codes are required without a recovery code', function (): void {
    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('disableAppAuthentication')
                ->schemaComponent('app', schema: 'content'),
            ['code' => ''],
        )
        ->assertHasFormErrors([
            'code' => 'required',
        ]);

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);
});

test('codes must be 6 digits', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('disableAppAuthentication')
                ->schemaComponent('app', schema: 'content'),
            ['code' => Str::limit($appAuthentication->getCurrentCode($user), limit: 5, end: '')],
        )
        ->assertHasFormErrors([
            'code' => 'digits',
        ]);

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);
});

it('will not disable authentication when an invalid recovery code is used', function (): void {
    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);

    livewire(EditProfile::class)
        ->mountAction(TestAction::make('disableAppAuthentication')
            ->schemaComponent('app', schema: 'content'))
        ->callAction(TestAction::make('useRecoveryCode')
            ->schemaComponent('code'))
        ->fillForm([
            'recoveryCode' => 'invalid-recovery-code',
        ])
        ->callMountedAction()
        ->assertHasFormErrors();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);
});

it('will not disable authentication with a recovery code if recovery is disabled', function (): void {
    Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders())
        ->recoverable(false);

    $user = auth()->user();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('disableAppAuthentication')
                ->schemaComponent('app', schema: 'content'),
            ['recoveryCode' => Arr::first($this->recoveryCodes)],
        )
        ->assertHasFormErrors();

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeTrue();

    expect($user->getAppAuthenticationSecret())
        ->not()->toBeNull();

    expect($user->getAppAuthenticationRecoveryCodes())
        ->toBeArray()
        ->toHaveCount(8);
});
