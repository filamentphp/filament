<?php

use Filament\Auth\Events\Registered;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Auth\Pages\Register;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    expect(Filament::getRegistrationUrl())->toEndWith('/register');

    $this->get(Filament::getRegistrationUrl())
        ->assertSuccessful();
});

it('can render page with a custom slug', function (): void {
    Filament::setCurrentPanel('slugs');

    expect(Filament::getRegistrationUrl())->toEndWith('/register-test');

    $this->get(Filament::getRegistrationUrl())
        ->assertSuccessful();
});

it('can register', function (): void {
    Notification::fake();
    Event::fake();

    $this->assertGuest();

    Filament::getCurrentOrDefaultPanel()->requiresEmailVerification(false);

    $userToRegister = User::factory()->make();

    livewire(Register::class)
        ->fillForm([
            'name' => $userToRegister->name,
            'email' => $userToRegister->email,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->call('register')
        ->assertRedirect(Filament::getUrl());

    Notification::assertSentTimes(VerifyEmail::class, expectedCount: 1);
    Event::assertDispatched(Registered::class);

    $this->assertAuthenticated();

    $this->assertCredentials([
        'email' => $userToRegister->email,
        'password' => 'password',
    ]);
});

it('can register and redirect user to their intended URL', function (): void {
    Notification::fake();
    Event::fake();

    session()->put('url.intended', $intendedUrl = Str::random());

    Filament::getCurrentOrDefaultPanel()->requiresEmailVerification(false);

    $userToRegister = User::factory()->make();

    livewire(Register::class)
        ->fillForm([
            'name' => $userToRegister->name,
            'email' => $userToRegister->email,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->call('register')
        ->assertRedirect($intendedUrl);
});

it('can throttle registration attempts', function (): void {
    Notification::fake();
    Event::fake();

    $this->assertGuest();

    foreach (range(1, 2) as $i) {
        $userToRegister = User::factory()->make();

        livewire(Register::class)
            ->fillForm([
                'name' => $userToRegister->name,
                'email' => $userToRegister->email,
                'password' => 'password',
                'passwordConfirmation' => 'password',
            ])
            ->call('register')
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticated();

        auth()->logout();
    }

    Notification::assertSentTimes(VerifyEmail::class, expectedCount: 2);
    Event::assertDispatchedTimes(Registered::class, times: 2);

    livewire(Register::class)
        ->fillForm([
            'name' => $userToRegister->name,
            'email' => $userToRegister->email,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->call('register')
        ->assertNotified()
        ->assertNoRedirect();

    Notification::assertSentTimes(VerifyEmail::class, expectedCount: 2);
    Event::assertDispatchedTimes(Registered::class, times: 2);

    $this->assertGuest();
});

it('can validate `name` is required', function (): void {
    livewire(Register::class)
        ->fillForm(['name' => ''])
        ->call('register')
        ->assertHasFormErrors(['name' => ['required']]);
});

it('can validate `name` is max 255 characters', function (): void {
    livewire(Register::class)
        ->fillForm(['name' => Str::random(256)])
        ->call('register')
        ->assertHasFormErrors(['name' => ['max']]);
});

it('can validate `email` is required', function (): void {
    livewire(Register::class)
        ->fillForm(['email' => ''])
        ->call('register')
        ->assertHasFormErrors(['email' => ['required']]);
});

it('can validate `email` is valid email', function (): void {
    livewire(Register::class)
        ->fillForm(['email' => 'invalid-email'])
        ->call('register')
        ->assertHasFormErrors(['email' => ['email']]);
});

it('can validate `email` is max 255 characters', function (): void {
    livewire(Register::class)
        ->fillForm(['email' => Str::random(256)])
        ->call('register')
        ->assertHasFormErrors(['email' => ['max']]);
});

it('can validate `email` is unique', function (): void {
    $existingEmail = User::factory()->create()->email;

    livewire(Register::class)
        ->fillForm(['email' => $existingEmail])
        ->call('register')
        ->assertHasFormErrors(['email' => ['unique']]);
});

it('can validate `password` is required', function (): void {
    livewire(Register::class)
        ->fillForm(['password' => ''])
        ->call('register')
        ->assertHasFormErrors(['password' => ['required']]);
});

it('can validate `password` is confirmed', function (): void {
    livewire(Register::class)
        ->fillForm([
            'password' => Str::random(),
            'passwordConfirmation' => Str::random(),
        ])
        ->call('register')
        ->assertHasFormErrors(['password' => ['same']]);
});

it('can validate `passwordConfirmation` is required', function (): void {
    livewire(Register::class)
        ->fillForm(['passwordConfirmation' => ''])
        ->call('register')
        ->assertHasFormErrors(['passwordConfirmation' => ['required']]);
});

it('can throttle registration attempts per email', function (): void {
    Notification::fake();
    Event::fake();

    $this->assertGuest();

    $email = fake()->unique()->safeEmail();

    // Clear the IP-based rate limiter between attempts to isolate the
    // email-based rate limit (simulates an attacker rotating IPs).
    $clearIpRateLimiter = function (): void {
        RateLimiter::clear('livewire-rate-limiter:' . sha1(Register::class . '|register|' . request()->ip()));
    };

    foreach (range(1, 2) as $i) {
        $clearIpRateLimiter();

        livewire(Register::class)
            ->fillForm([
                'name' => fake()->name(),
                'email' => $email,
                'password' => 'password',
                'passwordConfirmation' => 'password',
            ])
            ->call('register');

        // Only the first attempt actually registers (second will fail unique validation on email)
        if ($i === 1) {
            $this->assertAuthenticated();
            auth()->logout();
        }
    }

    $clearIpRateLimiter();

    // The 3rd attempt should be rate limited by email
    livewire(Register::class)
        ->fillForm([
            'name' => fake()->name(),
            'email' => $email,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->call('register')
        ->assertNotified()
        ->assertNoRedirect();

    $this->assertGuest();

    $clearIpRateLimiter();

    // A different email should not be affected
    $differentEmail = fake()->unique()->safeEmail();

    livewire(Register::class)
        ->fillForm([
            'name' => fake()->name(),
            'email' => $differentEmail,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->call('register')
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticated();
});
