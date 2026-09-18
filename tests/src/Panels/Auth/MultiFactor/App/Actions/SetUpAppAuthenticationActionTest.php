<?php

use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel('app-authentication');

    actingAs(User::factory()->create());
});

describe('setup flow', function (): void {
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
            ->fillForm([
                'code' => $appAuthentication->getCurrentCode($user, $secret),
                'password' => 'password',
            ])
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
                'password' => 'password',
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
});

describe('validation', function (): void {
    test('the user\'s current password is required', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $user = auth()->user();

        $livewire = livewire(EditProfile::class)
            ->mountAction(TestAction::make('setUpAppAuthentication')
                ->schemaComponent('app', schema: 'content'));

        $secret = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted'])['secret'];

        $livewire
            ->fillForm(['code' => $appAuthentication->getCurrentCode($user, $secret)])
            ->callMountedAction()
            ->assertHasFormErrors([
                'password' => 'required',
            ]);

        expect($user->getAppAuthenticationSecret())
            ->toBeEmpty();
    });

    test('the user\'s current password must be valid', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $user = auth()->user();

        $livewire = livewire(EditProfile::class)
            ->mountAction(TestAction::make('setUpAppAuthentication')
                ->schemaComponent('app', schema: 'content'));

        $secret = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted'])['secret'];

        $livewire
            ->fillForm([
                'code' => $appAuthentication->getCurrentCode($user, $secret),
                'password' => 'incorrect-password',
            ])
            ->callMountedAction()
            ->assertHasFormErrors([
                'password' => 'current_password',
            ]);

        expect($user->getAppAuthenticationSecret())
            ->toBeEmpty();
    });

    test('the user\'s current password is not validated when authentication attempts are rate limited', function (): void {
        $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

        $user = auth()->user();

        $rateLimitingKey = 'filament-set-up-app-authentication:' . $user->getAuthIdentifier();

        foreach (range(1, 5) as $attempt) {
            RateLimiter::hit($rateLimitingKey);
        }

        $livewire = livewire(EditProfile::class)
            ->mountAction(TestAction::make('setUpAppAuthentication')
                ->schemaComponent('app', schema: 'content'));

        $secret = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted'])['secret'];

        $livewire
            ->fillForm([
                'code' => $appAuthentication->getCurrentCode($user, $secret),
                'password' => 'incorrect-password',
            ])
            ->goToNextWizardStep()
            ->assertHasFormErrors([
                'password' => __('filament-panels::auth/multi-factor/app/actions/set-up.modal.form.code.messages.rate_limited'),
            ]);
    });

    test('password verification attempts are throttled when the code is blank', function (): void {
        $livewire = livewire(EditProfile::class)
            ->mountAction(TestAction::make('setUpAppAuthentication')
                ->schemaComponent('app', schema: 'content'));

        foreach (range(1, 5) as $attempt) {
            $livewire
                ->fillForm([
                    'code' => '',
                    'password' => "incorrect-password-{$attempt}",
                ])
                ->goToNextWizardStep()
                ->assertHasFormErrors([
                    'password' => 'current_password',
                ]);
        }

        $livewire
            ->fillForm([
                'code' => '',
                'password' => 'incorrect-password-6',
            ])
            ->goToNextWizardStep()
            ->assertHasFormErrors([
                'password' => __('filament-panels::auth/multi-factor/app/actions/set-up.modal.form.code.messages.rate_limited'),
            ]);
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
            ->fillForm([
                'code' => '',
                'password' => 'password',
            ])
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
                'password' => 'password',
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
});

it('can throttle code verification attempts per user', function (): void {
    $appAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    // Pre-fill the per-user rate limiter to simulate 5 prior attempts
    $rateLimitingKey = 'filament-set-up-app-authentication:' . $user->getAuthIdentifier();

    foreach (range(1, 5) as $i) {
        RateLimiter::hit($rateLimitingKey);
    }

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpAppAuthentication')
            ->schemaComponent('app', schema: 'content'));

    $encryptedActionArguments = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted']);
    $secret = $encryptedActionArguments['secret'];

    // Even with a valid code, the rate limit should block the attempt
    $livewire
        ->fillForm([
            'code' => $appAuthentication->getCurrentCode($user, $secret),
            'password' => 'password',
        ])
        ->callMountedAction()
        ->assertHasFormErrors([
            'password' => __('filament-panels::auth/multi-factor/app/actions/set-up.modal.form.code.messages.rate_limited'),
        ]);

    expect(filled($user->getAppAuthenticationSecret()))
        ->toBeFalse();
});
