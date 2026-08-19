<?php

use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\RateLimiter;
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

describe('disabling authentication', function (): void {
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

    it('can disable authentication with a one-time code after enabling the recovery code field', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $user = auth()->user();

        // Having enabled the recovery code field, the user can still change their mind and confirm with
        // their one-time code, leaving the recovery code blank.
        livewire(EditProfile::class)
            ->mountAction(TestAction::make('disableAppAuthentication')
                ->schemaComponent('app', schema: 'content'))
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent('code'))
            ->fillForm([
                'code' => $appAuthentication->getCurrentCode($user),
            ])
            ->callMountedAction()
            ->assertHasNoFormErrors();

        expect(filled($user->getAppAuthenticationSecret()))
            ->toBeFalse();
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
});

describe('validation', function (): void {
    test('a one-time code is still required when the recovery code field is enabled but left blank', function (): void {
        $user = auth()->user();

        // Enabling the recovery code field does not force the user down the recovery path: with the
        // recovery code left blank the one-time code is still required, so it can be used instead.
        livewire(EditProfile::class)
            ->mountAction(TestAction::make('disableAppAuthentication')
                ->schemaComponent('app', schema: 'content'))
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent('code'))
            ->callMountedAction()
            ->assertHasFormErrors([
                'code' => 'required',
            ]);

        expect(filled($user->getAppAuthenticationSecret()))
            ->toBeTrue();
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
});

describe('recovery code failures', function (): void {
    it('will not disable authentication when a recovery code is submitted without enabling the recovery code field', function (string $recoveryCode): void {
        $user = auth()->user();

        // The recovery code field is hidden until the user chooses to use a recovery code, so a value
        // present in the field while it is hidden must be ignored and the one-time code remains required.
        livewire(EditProfile::class)
            ->callAction(
                TestAction::make('disableAppAuthentication')
                    ->schemaComponent('app', schema: 'content'),
                ['recoveryCode' => $recoveryCode],
            )
            ->assertHasFormErrors([
                'code' => 'required',
            ]);

        expect(filled($user->getAppAuthenticationSecret()))
            ->toBeTrue();

        expect($user->getAppAuthenticationRecoveryCodes())
            ->toBeArray()
            ->toHaveCount(8);
    })->with([
        'an arbitrary string' => 'invalid-recovery-code',
        'a value that is not blank but resembles a falsy value' => '0',
        'a single character' => 'x',
    ]);

    it('will not disable authentication with a valid recovery code that is submitted without enabling the recovery code field', function (): void {
        $user = auth()->user();

        // A valid recovery code should only be honored once the user has enabled the recovery code field;
        // while it is hidden the code is neither validated nor consumed.
        livewire(EditProfile::class)
            ->callAction(
                TestAction::make('disableAppAuthentication')
                    ->schemaComponent('app', schema: 'content'),
                ['recoveryCode' => Arr::first($this->recoveryCodes)],
            )
            ->assertHasFormErrors([
                'code' => 'required',
            ]);

        expect(filled($user->getAppAuthenticationSecret()))
            ->toBeTrue();

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
});

describe('throttling', function (): void {
    it('can throttle code verification attempts per user', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $user = auth()->user();

        // Pre-fill the per-user rate limiter to simulate 5 prior attempts
        $rateLimitingKey = 'filament-disable-app-authentication:' . $user->getAuthIdentifier();

        foreach (range(1, 5) as $i) {
            RateLimiter::hit($rateLimitingKey);
        }

        // Even with a valid code, the rate limit should block the attempt
        livewire(EditProfile::class)
            ->callAction(
                TestAction::make('disableAppAuthentication')
                    ->schemaComponent('app', schema: 'content'),
                ['code' => $appAuthentication->getCurrentCode($user)],
            )
            ->assertHasFormErrors(['code']);

        expect(filled($user->getAppAuthenticationSecret()))
            ->toBeTrue();
    });

    it('can throttle recovery code verification attempts per user', function (): void {
        $user = auth()->user();

        // Pre-fill the per-user rate limiter to simulate 5 prior attempts
        $rateLimitingKey = 'filament-disable-app-authentication:' . $user->getAuthIdentifier();

        foreach (range(1, 5) as $i) {
            RateLimiter::hit($rateLimitingKey);
        }

        // Even with a valid recovery code, the rate limit should block the attempt
        livewire(EditProfile::class)
            ->mountAction(TestAction::make('disableAppAuthentication')
                ->schemaComponent('app', schema: 'content'))
            ->callAction(TestAction::make('useRecoveryCode')
                ->schemaComponent('code'))
            ->fillForm([
                'recoveryCode' => Arr::first($this->recoveryCodes),
            ])
            ->callMountedAction()
            ->assertHasFormErrors(['recoveryCode']);

        expect(filled($user->getAppAuthenticationSecret()))
            ->toBeTrue();
    });
});
