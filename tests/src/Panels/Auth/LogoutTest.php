<?php

use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Http\RedirectResponse;

uses(TestCase::class);

it('can log a user out', function (): void {
    $this
        ->actingAs(User::factory()->create())
        ->post(Filament::getLogoutUrl())
        ->assertRedirect(Filament::getLoginUrl());

    $this->assertGuest();
});

it('allows a user to override the `LogoutResponse`', function (): void {
    $logoutResponseFake = new class implements LogoutResponse
    {
        public function toResponse($request): RedirectResponse
        {
            return redirect()->to('https://example.com');
        }
    };

    $this->app->instance(LogoutResponse::class, $logoutResponseFake);

    $this
        ->actingAs(User::factory()->create())
        ->post(Filament::getLogoutUrl())
        ->assertRedirect('https://example.com');
});

it('redirects unauthenticated users when attempting to log out', function (): void {
    $this->assertGuest();

    $this
        ->post(Filament::getLogoutUrl())
        ->assertRedirect(Filament::getLoginUrl());
});
