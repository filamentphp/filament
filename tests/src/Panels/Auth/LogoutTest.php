<?php

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Filament\Tests\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Http\RedirectResponse;

uses(TestCase::class);

it('can log a user out', function () {
    $this
        ->actingAs(User::factory()->create())
        ->post(Filament::getLogoutUrl())
        ->assertRedirect(Filament::getLoginUrl());

    $this->assertGuest();
});

it('allows a user to override the logout response', function () {
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
