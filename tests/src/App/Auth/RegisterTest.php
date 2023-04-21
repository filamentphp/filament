<?php

use Filament\Facades\Filament;
use Filament\Pages\Auth\Register;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render page', function () {
    $this->get(Filament::getRegistrationUrl())->assertSuccessful();
});

it('can register', function () {
    Event::fake();

    $this->assertGuest();

    Filament::getCurrentContext()->requiresEmailVerification(false);

    $userToRegister = User::factory()->make();

    livewire(Register::class)
        ->fillForm([
            'name' => $userToRegister->name,
            'email' => $userToRegister->email,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->callAction('registerAction')
        ->assertRedirect(Filament::getUrl());

    Event::assertDispatched(Registered::class);

    $this->assertAuthenticated();

    $this->assertCredentials([
        'email' => $userToRegister->email,
        'password' => 'password',
    ]);
});

it('can register and redirect user to their intended URL', function () {
    session()->put('url.intended', $intendedUrl = Str::random());

    Filament::getCurrentContext()->requiresEmailVerification(false);

    $userToRegister = User::factory()->make();

    livewire(Register::class)
        ->fillForm([
            'name' => $userToRegister->name,
            'email' => $userToRegister->email,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->callAction('registerAction')
        ->assertRedirect($intendedUrl);
});

it('can throttle registration attempts', function () {
    Event::fake();

    $this->assertGuest();

    $userToRegister = User::factory()->make();

    livewire(Register::class)
        ->fillForm([
            'name' => $userToRegister->name,
            'email' => $userToRegister->email,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->callAction('registerAction')
        ->assertRedirect(Filament::getUrl());

    Event::assertDispatchedTimes(Registered::class, times: 1);

    $this->assertAuthenticated();

    auth()->logout();

    livewire(Register::class)
        ->fillForm([
            'name' => $userToRegister->name,
            'email' => $userToRegister->email,
            'password' => 'password',
            'passwordConfirmation' => 'password',
        ])
        ->callAction('registerAction')
        ->assertNotified()
        ->assertNoRedirect();

    Event::assertDispatchedTimes(Registered::class, times: 1);

    $this->assertGuest();
});

it('can validate `name` is required', function () {
    livewire(Register::class)
        ->fillForm(['name' => ''])
        ->callAction('registerAction')
        ->assertHasErrors(['name' => ['required']]);
});

it('can validate `name` is max 255 characters', function () {
    livewire(Register::class)
        ->fillForm(['name' => Str::random(256)])
        ->callAction('registerAction')
        ->assertHasErrors(['name' => ['max']]);
});

it('can validate `email` is required', function () {
    livewire(Register::class)
        ->fillForm(['email' => ''])
        ->callAction('registerAction')
        ->assertHasErrors(['email' => ['required']]);
});

it('can validate `email` is valid email', function () {
    livewire(Register::class)
        ->fillForm(['email' => 'invalid-email'])
        ->callAction('registerAction')
        ->assertHasErrors(['email' => ['email']]);
});

it('can validate `email` is max 255 characters', function () {
    livewire(Register::class)
        ->fillForm(['email' => Str::random(256)])
        ->callAction('registerAction')
        ->assertHasErrors(['email' => ['max']]);
});

it('can validate `email` is unique', function () {
    $existingEmail = User::factory()->create()->email;

    livewire(Register::class)
        ->fillForm(['email' => $existingEmail])
        ->callAction('registerAction')
        ->assertHasErrors(['email' => ['unique']]);
});

it('can validate `password` is required', function () {
    livewire(Register::class)
        ->fillForm(['password' => ''])
        ->callAction('registerAction')
        ->assertHasErrors(['password' => ['required']]);
});

it('can validate `password` is confirmed', function () {
    livewire(Register::class)
        ->fillForm([
            'password' => Str::random(),
            'passwordConfirmation' => Str::random(),
        ])
        ->callAction('registerAction')
        ->assertHasErrors(['password' => ['same']]);
});

it('can validate `passwordConfirmation` is required', function () {
    livewire(Register::class)
        ->fillForm(['passwordConfirmation' => ''])
        ->callAction('registerAction')
        ->assertHasErrors(['passwordConfirmation' => ['required']]);
});
