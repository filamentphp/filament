<?php

use Filament\Auth\Notifications\NoticeOfEmailChangeRequest;
use Filament\Auth\Notifications\VerifyEmailChange;
use Filament\Auth\Pages\EditProfile;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use League\Uri\Components\Query;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

it('can render page', function (): void {
    $this->get(Filament::getProfileUrl())
        ->assertSuccessful();
});

it('can retrieve data', function (): void {
    livewire(EditProfile::class)
        ->assertFormSet([
            'name' => $this->user->name,
            'email' => $this->user->email,
        ]);
});

it('can save name', function (): void {
    $newUserData = User::factory()->make();

    livewire(EditProfile::class)
        ->fillForm([
            'name' => $newUserData->name,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified('Saved');

    expect($this->user->refresh())
        ->name->toBe($newUserData->name);
});

it('can save email', function (): void {
    Filament::getCurrentOrDefaultPanel()->emailChangeVerification(false);

    $newUserData = User::factory()->make();

    livewire(EditProfile::class)
        ->fillForm([
            'email' => $newUserData->email,
            'currentPassword' => 'password',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified('Saved');

    expect($this->user->refresh())
        ->email->toBe($newUserData->email);
});

it('can send email change verification', function (): void {
    Notification::fake();

    Filament::getCurrentOrDefaultPanel()->emailChangeVerification();

    $oldEmail = $this->user->email;

    $newUserData = User::factory()->make();

    livewire(EditProfile::class)
        ->fillForm([
            'email' => $newUserData->email,
            'currentPassword' => 'password',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified('Email address change request sent')
        ->assertFormSet([
            'email' => $oldEmail,
        ]);

    expect($this->user->refresh())
        ->email->toBe($oldEmail);

    Notification::assertSentTo($this->user, NoticeOfEmailChangeRequest::class, function (NoticeOfEmailChangeRequest $notification) use ($newUserData): bool {
        if (blank($notification->blockVerificationUrl)) {
            return false;
        }

        return $notification->newEmail === $newUserData->email;
    });

    Notification::assertSentOnDemand(VerifyEmailChange::class, function (VerifyEmailChange $notification): bool {
        $verificationSignature = Query::new($notification->url)->get('signature');

        return cache()->has($verificationSignature);
    });
});

it('can save password', function (): void {
    expect(Filament::auth()->attempt([
        'email' => $this->user->email,
        'password' => 'password',
    ]))->toBeTrue();

    $newPassword = Str::random();

    livewire(EditProfile::class)
        ->fillForm([
            'password' => $newPassword,
            'passwordConfirmation' => $newPassword,
            'currentPassword' => 'password',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified('Saved')
        ->assertFormSet([
            'password' => '',
            'passwordConfirmation' => '',
        ]);

    expect(Filament::auth()->attempt([
        'email' => $this->user->email,
        'password' => 'password',
    ]))->toBeFalse();

    expect(Filament::auth()->attempt([
        'email' => $this->user->email,
        'password' => $newPassword,
    ]))->toBeTrue();
});

it('can validate', function (array $formData, array $errors): void {
    Filament::getCurrentOrDefaultPanel()->emailChangeVerification(false);

    livewire(EditProfile::class)
        ->fillForm($formData)
        ->call('save')
        ->assertHasFormErrors($errors)
        ->assertNotNotified('Saved');

    $this->user->refresh();

    foreach ($formData as $key => $value) {
        expect($this->user->getAttributeValue($key))
            ->not->toBe($value);
    }
})->with([
    '`name` is required' => [
        ['name' => '', 'email' => fake()->email],
        ['name' => ['required']],
    ],
    '`name` is max 255 characters' => [
        ['name' => Str::random(256), 'email' => fake()->email],
        ['name' => ['max']],
    ],
    '`email` is required' => [
        ['name' => fake()->name, 'email' => ''],
        ['email' => ['required']],
    ],
    '`email` is valid email address' => [
        ['name' => fake()->name, 'email' => 'not-an-email'],
        ['email' => ['email']],
    ],
    '`email` is unique' => fn (): array => [
        ['name' => fake()->name, 'email' => User::factory()->create()->email],
        ['email' => ['unique']],
    ],
    '`password` is confirmed' => fn (): array => [
        ['name' => fake()->name, 'email' => fake()->email, 'password' => Str::random(), 'passwordConfirmation' => Str::random()],
        ['password' => ['same']],
    ],
    '`passwordConfirmation` is required when `password` is filled' => fn (): array => [
        ['name' => fake()->name, 'email' => fake()->email, 'password' => Str::random(), 'passwordConfirmation' => ''],
        ['passwordConfirmation' => ['required']],
    ],
    '`currentPassword` is required when `password` is filled' => fn (): array => [
        ['name' => fake()->name, 'email' => fake()->email, 'password' => Str::random(), 'currentPassword' => ''],
        ['currentPassword' => ['required']],
    ],
    '`currentPassword` is required when `email` is changed' => fn (): array => [
        ['name' => fake()->name, 'email' => fake()->email, 'currentPassword' => ''],
        ['currentPassword' => ['required']],
    ],
    '`currentPassword` is valid password' => fn (): array => [
        ['name' => fake()->name, 'email' => fake()->email, 'currentPassword' => 'invalid-password'],
        ['currentPassword' => ['current_password']],
    ],
]);
