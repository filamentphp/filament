<?php

use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render page', function (): void {
    expect(Filament::getLoginUrl())->toEndWith('/login');

    $this->get(Filament::getLoginUrl())
        ->assertSuccessful();
});

it('can render page with a custom slug', function (): void {
    Filament::setCurrentPanel('slugs');

    expect(Filament::getLoginUrl())->toEndWith('/login-test');

    $this->get(Filament::getLoginUrl())
        ->assertSuccessful();
});

it('can authenticate', function (): void {
    $this->assertGuest();

    $userToAuthenticate = User::factory()->create();

    livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticatedAs($userToAuthenticate);
});

it('can authenticate and redirect user to their intended URL', function (): void {
    session()->put('url.intended', $intendedUrl = Str::random());

    $userToAuthenticate = User::factory()->create();

    livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect($intendedUrl);
});

it('can redirect unauthenticated app requests', function (): void {
    $this->get(route('filament.admin.pages.dashboard'))->assertRedirect(Filament::getLoginUrl());
});

it('cannot authenticate with incorrect credentials', function (): void {
    $userToAuthenticate = User::factory()->create();

    livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'incorrect-password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

it('cannot authenticate on unauthorized panel', function (): void {
    $userToAuthenticate = User::factory()->create();

    Filament::setCurrentPanel('custom');

    livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

it('can throttle authentication attempts', function (): void {
    $this->assertGuest();

    $userToAuthenticate = User::factory()->create();

    foreach (range(1, 5) as $i) {
        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate');

        $this->assertAuthenticated();

        auth()->logout();
    }

    livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertNotified();

    $this->assertGuest();
});

it('can validate `email` is required', function (): void {
    livewire(Login::class)
        ->fillForm(['email' => ''])
        ->call('authenticate')
        ->assertHasFormErrors(['email' => ['required']]);
});

it('can validate `email` is valid email', function (): void {
    livewire(Login::class)
        ->fillForm(['email' => 'invalid-email'])
        ->call('authenticate')
        ->assertHasFormErrors(['email' => ['email']]);
});

it('can validate `password` is required', function (): void {
    livewire(Login::class)
        ->fillForm(['password' => ''])
        ->call('authenticate')
        ->assertHasFormErrors(['password' => ['required']]);
});
