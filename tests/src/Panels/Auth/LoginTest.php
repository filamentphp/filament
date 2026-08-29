<?php

use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Sleep;
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

describe('authentication', function (): void {
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

    it('rotates the session ID after a successful authentication to prevent session fixation', function (): void {
        $userToAuthenticate = User::factory()->create();

        $sessionIdBefore = session()->getId();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertRedirect(Filament::getUrl());

        expect(session()->getId())->not->toBe($sessionIdBefore);
    });

    it('rotates the CSRF token after a successful authentication', function (): void {
        $userToAuthenticate = User::factory()->create();

        session()->start();
        $csrfTokenBefore = session()->token();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertRedirect(Filament::getUrl());

        expect(session()->token())->not->toBe($csrfTokenBefore);
    });

    it('does not rotate the CSRF token when authentication fails (control)', function (): void {
        $userToAuthenticate = User::factory()->create();

        session()->start();
        $csrfTokenBefore = session()->token();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'wrong-password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        expect(session()->token())->toBe($csrfTokenBefore);
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

    it('does not pad a successful authentication when the panel registers no multi-factor authentication providers', function (): void {
        expect(Filament::getMultiFactorAuthenticationProviders())->toBe([]);

        $userToAuthenticate = User::factory()->create();

        $padding = [];

        Sleep::fake();
        Sleep::whenFakingSleep(function ($duration) use (&$padding): void {
            $padding[] = $duration->totalMilliseconds;
        });

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertRedirect(Filament::getUrl());

        expect($padding)->toBe([]);
    });

    it('still pads a successful authentication when the panel registers a multi-factor authentication provider (control)', function (): void {
        Filament::setCurrentPanel('app-authentication');

        $userToAuthenticate = User::factory()->create();

        $padding = [];

        Sleep::fake();
        Sleep::whenFakingSleep(function ($duration) use (&$padding): void {
            $padding[] = $duration->totalMilliseconds;
        });

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        expect($padding)->not->toBe([]);
    });

    it('retrieves and verifies the credentials only once', function (): void {
        Event::fake([Attempting::class, Validated::class]);

        $userToAuthenticate = User::factory()->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertRedirect(Filament::getUrl());

        Event::assertDispatchedTimes(Attempting::class, 1);
        Event::assertDispatchedTimes(Validated::class, 1);
    });

    it('timeboxes panel access rechecks after `Validated` listeners run', function (): void {
        $userToAuthenticate = User::factory()->create();

        Event::listen(Validated::class, static function (): void {
            Filament::setCurrentPanel('custom');
        });

        config()->set('auth.timebox_duration', 10_000_000);

        $padding = [];

        Sleep::fake();
        Sleep::whenFakingSleep(function ($duration) use (&$padding): void {
            $padding[] = $duration->totalMilliseconds;
        });

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();

        expect($padding)->toHaveCount(1);
    });

    it('rehashes a password that was stored at a lower cost', function (): void {
        $userToAuthenticate = User::factory()->create([
            'password' => Hash::make('password', ['rounds' => 4]),
        ]);

        config()->set('hashing.bcrypt.rounds', 5);
        Hash::forgetDrivers();

        expect(Hash::needsRehash($userToAuthenticate->password))->toBeTrue();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertRedirect(Filament::getUrl());

        expect(Hash::needsRehash($userToAuthenticate->refresh()->password))->toBeFalse();
    });

    it('does not rehash the password when `hashing.rehash_on_login` is disabled', function (): void {
        $userToAuthenticate = User::factory()->create([
            'password' => Hash::make('password', ['rounds' => 4]),
        ]);

        config()->set('hashing.rehash_on_login', false);
        config()->set('hashing.bcrypt.rounds', 5);
        Hash::forgetDrivers();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertRedirect(Filament::getUrl());

        expect(Hash::needsRehash($userToAuthenticate->refresh()->password))->toBeTrue();
    });

});

describe('authentication failures', function (): void {
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

    it('fires the `Attempting` event when authentication fails because the email is unknown', function (): void {
        Event::fake([Attempting::class]);

        livewire(Login::class)
            ->fillForm([
                'email' => 'nonexistent@example.com',
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        Event::assertDispatched(function (Attempting $event): bool {
            if ($event->guard !== 'web') {
                return false;
            }

            return $event->credentials === [
                'email' => 'nonexistent@example.com',
                'password' => 'password',
            ];
        });
    });

    it('fires the `Attempting` event when authentication fails because the password is wrong', function (): void {
        Event::fake([Attempting::class]);

        $userToAuthenticate = User::factory()->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'incorrect-password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        Event::assertDispatched(function (Attempting $event) use ($userToAuthenticate): bool {
            if ($event->guard !== 'web') {
                return false;
            }

            return $event->credentials === [
                'email' => $userToAuthenticate->email,
                'password' => 'incorrect-password',
            ];
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

    it('cannot reach the multi-factor challenge on a panel that `canAccessPanel()` denies', function (): void {
        Event::fake([Failed::class]);

        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        Filament::setCurrentPanel('inaccessible-multi-factor-authentication');

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertSet('userUndertakingMultiFactorAuthentication', null)
            ->assertHasFormErrors(['email']);

        $this->assertGuest();

        Event::assertDispatched(function (Failed $event) use ($userToAuthenticate): bool {
            if ($event->guard !== 'web') {
                return false;
            }

            return $event->user->is($userToAuthenticate);
        });
    });

    it('returns the same failure for a correct and an incorrect password on a panel that `canAccessPanel()` denies', function (): void {
        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        Filament::setCurrentPanel('inaccessible-multi-factor-authentication');

        $getFailure = function (string $password) use ($userToAuthenticate): array {
            $livewire = livewire(Login::class)
                ->fillForm([
                    'email' => $userToAuthenticate->email,
                    'password' => $password,
                ])
                ->call('authenticate');

            return [
                'userUndertakingMultiFactorAuthentication' => $livewire->get('userUndertakingMultiFactorAuthentication'),
                'errors' => $livewire->errors()->toArray(),
            ];
        };

        expect($getFailure('password'))->toEqual($getFailure('incorrect-password'));
    });

    it('does not send an email authentication code to a user that `canAccessPanel()` denies', function (): void {
        Notification::fake();

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->create();

        Filament::setCurrentPanel('inaccessible-multi-factor-authentication');

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        Notification::assertNotSentTo($userToAuthenticate, VerifyEmailAuthentication::class);

        $this->assertGuest();
    });

    it('applies the same timebox padding to an incorrect password and to an account denied by `canAccessPanel()`', function (string $panel): void {
        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        Filament::setCurrentPanel($panel);

        // The number of `Timebox` delays is compared between the two paths rather than
        // asserted against a literal, because no padding happens at all when the
        // password hash costs more than `auth.timebox_duration` to verify.
        $getPadding = function (string $password) use ($userToAuthenticate): array {
            $padding = [];

            Sleep::fake();
            Sleep::whenFakingSleep(function ($duration) use (&$padding): void {
                $padding[] = $duration->totalMilliseconds;
            });

            livewire(Login::class)
                ->fillForm([
                    'email' => $userToAuthenticate->email,
                    'password' => $password,
                ])
                ->call('authenticate')
                ->assertHasFormErrors(['email']);

            return $padding;
        };

        $correctPasswordPadding = $getPadding('password');
        $incorrectPasswordPadding = $getPadding('incorrect-password');

        expect($correctPasswordPadding)->toHaveCount(count($incorrectPasswordPadding));
    })->with(['custom', 'inaccessible-multi-factor-authentication']);

    it('still presents the multi-factor challenge on a panel that `canAccessPanel()` allows (control)', function (): void {
        $userToAuthenticate = User::factory()
            ->hasAppAuthentication()
            ->create();

        Filament::setCurrentPanel('app-authentication');

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertHasNoFormErrors();

        $this->assertGuest();
    });

});

describe('rate limiting', function (): void {
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

});

describe('validation', function (): void {
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

});

it('can fill the login form, authenticate, and redirect to the dashboard in the browser', function (): void {
    retry(10, function (): void {
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
