<?php

use Filament\Actions\Testing\TestAction;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

use function Filament\Tests\livewire;
use function Pest\Laravel\actingAs;

uses(TestCase::class);

beforeEach(function (): void {
    Filament::setCurrentPanel('email-authentication');

    actingAs(User::factory()->create());

    Notification::fake();
});

it('can generate a secret when the action is mounted', function (): void {
    /** @var EmailAuthentication $emailAuthentication */
    $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $emailAuthentication->generateCodesUsing(fn (): string => $code);

    livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailAuthentication')
            ->schemaComponent('email_code', schema: 'content'));

    Notification::assertSentTo(auth()->user(), VerifyEmailAuthentication::class, function (VerifyEmailAuthentication $notification) use ($code, $emailAuthentication): bool {
        if ($notification->codeExpiryMinutes !== $emailAuthentication->getCodeExpiryMinutes()) {
            return false;
        }

        return $notification->code === $code;
    });
});

it('can enable email authentication', function (): void {
    /** @var EmailAuthentication $emailAuthentication */
    $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect($user->hasEmailAuthentication())
        ->toBeFalse();

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $emailAuthentication->generateCodesUsing(fn (): string => $code);

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailAuthentication')
            ->schemaComponent('email_code', schema: 'content'));

    $livewire
        ->fillForm(['code' => $code])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    expect($user->hasEmailAuthentication())
        ->toBeTrue();
});

it('can resend the code to the user', function (): void {
    $this->travelTo(now()->subMinute());

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailAuthentication')
            ->schemaComponent('email_code', schema: 'content'));

    Notification::assertSentTimes(VerifyEmailAuthentication::class, 1);

    $this->travelBack();

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent('code'));

    Notification::assertSentTimes(VerifyEmailAuthentication::class, 2);
});

it('can resend the code to the user more than twice per minute', function (): void {
    $this->travelTo(now()->subMinute());

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailAuthentication')
            ->schemaComponent('email_code', schema: 'content'));

    Notification::assertSentTimes(VerifyEmailAuthentication::class, 1);

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent('code'));

    Notification::assertSentTimes(VerifyEmailAuthentication::class, 2);

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent('code'));

    Notification::assertSentTimes(VerifyEmailAuthentication::class, 2);

    $this->travelBack();

    $livewire
        ->callAction(TestAction::make('resend')
            ->schemaComponent('code'));

    Notification::assertSentTimes(VerifyEmailAuthentication::class, 3);
});

it('will not set up authentication when an invalid code is used', function (): void {
    $user = auth()->user();

    expect($user->hasEmailAuthentication())
        ->toBeFalse();

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailAuthentication')
            ->schemaComponent('email_code', schema: 'content'));

    $livewire
        ->fillForm([
            'code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
        ])
        ->callMountedAction()
        ->assertHasFormErrors();

    expect($user->hasEmailAuthentication())
        ->toBeFalse();
});

test('codes are required', function (): void {
    $user = auth()->user();

    expect($user->hasEmailAuthentication())
        ->toBeFalse();

    livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailAuthentication')
            ->schemaComponent('email_code', schema: 'content'))
        ->fillForm(['code' => ''])
        ->callMountedAction()
        ->assertHasFormErrors([
            'code' => 'required',
        ]);

    expect($user->hasEmailAuthentication())
        ->toBeFalse();
});

test('codes must be 6 digits', function (): void {
    $user = auth()->user();

    expect($user->hasEmailAuthentication())
        ->toBeFalse();

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('setUpEmailAuthentication')
            ->schemaComponent('email_code', schema: 'content'));

    $livewire
        ->fillForm([
            'code' => str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT),
        ])
        ->callMountedAction()
        ->assertHasFormErrors([
            'code' => 'digits',
        ]);

    expect($user->hasEmailAuthentication())
        ->toBeFalse();
});
