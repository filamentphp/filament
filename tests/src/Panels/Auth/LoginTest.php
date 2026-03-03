<?php

use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

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
    Event::fake([Failed::class]);

    $userToAuthenticate = User::factory()->create();

    livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'incorrect-password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();

    Event::assertDispatched(function (Failed $event) use ($userToAuthenticate) {
        if ($event->guard !== 'web') {
            return false;
        }

        if (! $event->user->is($userToAuthenticate)) {
            return false;
        }

        if ($event->credentials !== [
            'email' => $userToAuthenticate->email,
            'password' => 'incorrect-password',
        ]) {
            return false;
        }

        return true;
    });
});

it('cannot authenticate on unauthorized panel', function (): void {
    Event::fake([Failed::class]);

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

    Event::assertDispatched(function (Failed $event) use ($userToAuthenticate) {
        if ($event->guard !== 'web') {
            return false;
        }

        if (! $event->user->is($userToAuthenticate)) {
            return false;
        }

        if ($event->credentials !== [
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ]) {
            return false;
        }

        return true;
    });
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

it('can fill the login form, authenticate, and redirect to the dashboard in the browser', function (): void {
    $user = User::factory()->create();

    visit(Filament::getLoginUrl())
        ->assertSee('Sign in')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues()
        ->type('input[type="email"]', $user->email)
        ->type('input[type="password"]', 'password')
        ->click('button[type="submit"]')
        ->assertSee('Dashboard')
        ->assertPathIs('/')
        ->assertSee('Dashboard')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    visit(Filament::getLoginUrl())
        ->inDarkMode()
        ->assertNoAccessibilityIssues();

    visit(Filament::getUrl())
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});

it('can throttle login attempts per IP and email', function (): void {
    $this->assertGuest();

    $userToAuthenticate = User::factory()->create();

    // Clear the IP-only rate limiter between attempts to isolate the
    // IP+email rate limit.
    $clearIpRateLimiter = function (): void {
        RateLimiter::clear('livewire-rate-limiter:' . sha1(Login::class . '|authenticate|' . request()->ip()));
    };

    foreach (range(1, 5) as $i) {
        $clearIpRateLimiter();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate');

        $this->assertAuthenticated();

        auth()->logout();
    }

    $clearIpRateLimiter();

    // The 6th attempt from the same IP + email should be rate limited
    livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertNotified();

    $this->assertGuest();

    $clearIpRateLimiter();

    // A different email from the same IP should not be affected
    $secondUser = User::factory()->create();

    livewire(Login::class)
        ->fillForm([
            'email' => $secondUser->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticatedAs($secondUser);
});

it('does not lock out a user when an attacker exhausts login attempts from a different IP', function (): void {
    $this->assertGuest();

    $userToAuthenticate = User::factory()->create();

    // Simulate an attacker exhausting login attempts from a different IP.
    $attackerIp = '192.168.1.100';
    $attackerKey = 'filament-login:' . sha1($attackerIp . '|' . $userToAuthenticate->email);

    foreach (range(1, 5) as $i) {
        RateLimiter::hit($attackerKey);
    }

    // The legitimate user on a different IP should still be able to log in.
    livewire(Login::class)
        ->fillForm([
            'email' => $userToAuthenticate->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect(Filament::getUrl());

    $this->assertAuthenticatedAs($userToAuthenticate);
});
