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

    actingAs(User::factory()
        ->hasEmailAuthentication()
        ->create());

    Notification::fake();
});

it('can disable authentication when valid challenge code is used', function (): void {
    /** @var EmailAuthentication $emailAuthentication */
    $emailAuthentication = Arr::first(Filament::getCurrentOrDefaultPanel()->getMultiFactorAuthenticationProviders());

    $user = auth()->user();

    expect($user->hasEmailAuthentication())
        ->toBeTrue();

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $emailAuthentication->generateCodesUsing(fn (): string => $code);

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('disableEmailAuthentication')
                ->schemaComponent('email_code', schema: 'content'),
            ['code' => $code],
        )
        ->assertHasNoFormErrors();

    expect($user->hasEmailAuthentication())
        ->toBeFalse();

    Notification::assertSentTo($user, VerifyEmailAuthentication::class, function (VerifyEmailAuthentication $notification) use ($code, $emailAuthentication): bool {
        if ($notification->codeExpiryMinutes !== $emailAuthentication->getCodeExpiryMinutes()) {
            return false;
        }

        return $notification->code === $code;
    });
});

it('can resend the code to the user', function (): void {
    $this->travelTo(now()->subMinute());

    $livewire = livewire(EditProfile::class)
        ->mountAction(TestAction::make('disableEmailAuthentication')
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
        ->mountAction(TestAction::make('disableEmailAuthentication')
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

it('will not disable authentication when an invalid code is used', function (): void {
    $user = auth()->user();

    expect($user->hasEmailAuthentication())
        ->toBeTrue();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('disableEmailAuthentication')
                ->schemaComponent('email_code', schema: 'content'),
            ['code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT)],
        )
        ->assertHasFormErrors();

    expect($user->hasEmailAuthentication())
        ->toBeTrue();
});

test('codes are required', function (): void {
    $user = auth()->user();

    expect($user->hasEmailAuthentication())
        ->toBeTrue();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('disableEmailAuthentication')
                ->schemaComponent('email_code', schema: 'content'),
            ['code' => ''],
        )
        ->assertHasFormErrors([
            'code' => 'required',
        ]);

    expect($user->hasEmailAuthentication())
        ->toBeTrue();
});

test('codes must be 6 digits', function (): void {
    $user = auth()->user();

    expect($user->hasEmailAuthentication())
        ->toBeTrue();

    livewire(EditProfile::class)
        ->callAction(
            TestAction::make('disableEmailAuthentication')
                ->schemaComponent('email_code', schema: 'content'),
            ['code' => str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT)],
        )
        ->assertHasFormErrors([
            'code' => 'digits',
        ]);

    expect($user->hasEmailAuthentication())
        ->toBeTrue();
});
