<?php

use Filament\Actions\Testing\Fixtures\TestAction;
use Filament\Auth\MultiFactor\EmailCode\Notifications\VerifyEmailCodeAuthentication;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel('email-code-authentication');

    actingAs(User::factory()->create());

    Notification::fake();
});

it('can generate a secret when the action is mounted', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailCodeAuthentication')
            ->schemaComponent('content.email_code'))
        ->assertActionMounted(TestAction::make('setUpEmailCodeAuthentication')
            ->schemaComponent('content.email_code')
            ->arguments(function (array $actualArguments): bool {
                $encrypted = decrypt($actualArguments['encrypted']);

                if (blank($encrypted['secret'] ?? null)) {
                    return false;
                }

                if (blank($encrypted['userId'] ?? null)) {
                    return false;
                }

                return $encrypted['userId'] === auth()->id();
            }));

    $encryptedActionArguments = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted']);
    $secret = $encryptedActionArguments['secret'];

    Notification::assertSentTo(auth()->user(), VerifyEmailCodeAuthentication::class, function (VerifyEmailCodeAuthentication $notification) use ($emailCodeAuthentication, $secret): bool {
        if ($notification->codeWindow !== $emailCodeAuthentication->getCodeWindow()) {
            return false;
        }

        return $notification->code === $emailCodeAuthentication->getCurrentCode(auth()->user(), $secret);
    });
});

it('can save the secret to the user when the action is submitted', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect($user->hasEmailCodeAuthentication())
        ->toBeFalse();

    expect($user->getEmailCodeAuthenticationSecret())
        ->toBeEmpty();

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailCodeAuthentication')
            ->schemaComponent('content.email_code'));

    $encryptedActionArguments = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted']);
    $secret = $encryptedActionArguments['secret'];

    $livewire
        ->setActionData(['code' => $emailCodeAuthentication->getCurrentCode($user, $secret)])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    expect($user->hasEmailCodeAuthentication())
        ->toBeTrue();

    expect($user->getEmailCodeAuthenticationSecret())
        ->toBe($secret);
});

it('can resend the code to the user', function (): void {
    $this->travelTo(now()->subMinute());

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailCodeAuthentication')
            ->schemaComponent('content.email_code'));

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 1);

    $this->travelBack();

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent('mountedActionSchema0.code'));

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 2);
});

it('can resend the code to the user more than once per minute', function (): void {
    $this->travelTo(now()->subMinute());

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailCodeAuthentication')
            ->schemaComponent('content.email_code'));

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 1);

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent('mountedActionSchema0.code'));

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 1);

    $this->travelBack();

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent('mountedActionSchema0.code'));

    Notification::assertSentTimes(VerifyEmailCodeAuthentication::class, 2);
});

it('will not set up authentication when an invalid code is used', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect($user->hasEmailCodeAuthentication())
        ->toBeFalse();

    expect($user->getEmailCodeAuthenticationSecret())
        ->toBeEmpty();

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailCodeAuthentication')
            ->schemaComponent('content.email_code'));

    $encryptedActionArguments = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted']);
    $secret = $encryptedActionArguments['secret'];

    $livewire
        ->setActionData([
            'code' => ($emailCodeAuthentication->getCurrentCode($user, $secret) === '000000') ? '111111' : '000000',
        ])
        ->callMountedAction()
        ->assertHasActionErrors();

    expect($user->hasEmailCodeAuthentication())
        ->toBeFalse();

    expect($user->getEmailCodeAuthenticationSecret())
        ->toBeEmpty();
});

test('codes are required', function (): void {
    $user = auth()->user();

    expect($user->hasEmailCodeAuthentication())
        ->toBeFalse();

    expect($user->getEmailCodeAuthenticationSecret())
        ->toBeEmpty();

    livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailCodeAuthentication')
            ->schemaComponent('content.email_code'))
        ->setActionData(['code' => ''])
        ->callMountedAction()
        ->assertHasActionErrors([
            'code' => 'required',
        ]);

    expect($user->hasEmailCodeAuthentication())
        ->toBeFalse();

    expect($user->getEmailCodeAuthenticationSecret())
        ->toBeEmpty();
});

test('codes must be 6 digits', function (): void {
    $emailCodeAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect($user->hasEmailCodeAuthentication())
        ->toBeFalse();

    expect($user->getEmailCodeAuthenticationSecret())
        ->toBeEmpty();

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailCodeAuthentication')
            ->schemaComponent('content.email_code'));

    $encryptedActionArguments = decrypt($livewire->instance()->mountedActions[0]['arguments']['encrypted']);
    $secret = $encryptedActionArguments['secret'];

    $livewire
        ->setActionData([
            'code' => Str::limit($emailCodeAuthentication->getCurrentCode($user, $secret), limit: 5, end: ''),
        ])
        ->callMountedAction()
        ->assertHasActionErrors([
            'code' => 'digits',
        ]);

    expect($user->hasEmailCodeAuthentication())
        ->toBeFalse();

    expect($user->getEmailCodeAuthenticationSecret())
        ->toBeEmpty();
});
