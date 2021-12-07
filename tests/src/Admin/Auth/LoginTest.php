<?php

use Filament\Http\Livewire\Auth\Login;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;
use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can render page', function () {
    $this->get(route('filament.auth.login'))->assertSuccessful();
});

it('can authenticate', function () {
    $this->assertGuest();

    $userToAuthenticate = User::factory()->create();

    livewire(Login::class)
        ->set('email', $userToAuthenticate->email)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertRedirect(route('filament.pages.dashboard'));

    $this->assertAuthenticatedAs($userToAuthenticate);
});

it('can authenticate and redirect user to their intended URL', function () {
    session()->put('url.intended', $intendedUrl = Str::random());

    $userToAuthenticate = User::factory()->create();

    livewire(Login::class)
        ->set('email', $userToAuthenticate->email)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertRedirect($intendedUrl);
});

it('can redirect unauthenticated app requests', function () {
    $this->get(route('filament.pages.dashboard'))->assertRedirect(route('filament.auth.login'));
});

it('cannot authenticate with incorrect credentials', function () {
    $userToAuthenticate = User::factory()->create();

    livewire(Login::class)
        ->set('email', $userToAuthenticate->email)
        ->set('password', 'incorrect-password')
        ->call('authenticate')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

it('can throttle authentication attempts', function () {
    $this->assertGuest();

    $userToAuthenticate = User::factory()->create();

    foreach (range(1, 5) as $i) {
        livewire(Login::class)
            ->set('email', $userToAuthenticate->email)
            ->set('password', 'password')
            ->call('authenticate');

        $this->assertAuthenticated();

        auth()->logout();
    }

    livewire(Login::class)
        ->set('email', $userToAuthenticate->email)
        ->set('password', 'password')
        ->call('authenticate')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

it('can validate `email` is required', function () {
    livewire(Login::class)
        ->assertSet('email', '')
        ->call('authenticate')
        ->assertHasErrors(['email' => ['required']]);
});

it('can validate `email` is valid email', function () {
    livewire(Login::class)
        ->set('email', 'invalid-email')
        ->call('authenticate')
        ->assertHasErrors(['email' => ['email']]);
});

it('can validate `password` is required', function () {
    livewire(Login::class)
        ->assertSet('password', '')
        ->call('authenticate')
        ->assertHasErrors(['password' => ['required']]);
});
