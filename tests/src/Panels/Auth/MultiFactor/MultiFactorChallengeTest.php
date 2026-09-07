<?php

use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Auth\MultiFactor\MultiFactorChallenge;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\MultiFactorChallengeBrowserTest;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Mockery\MockInterface;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');

    Filament::setCurrentPanel('required-multi-factor-authentication');

    Notification::fake();
});

describe('enabled providers', function (): void {
    it('only returns the providers that the user has enabled', function (): void {
        $user = User::factory()
            ->hasEmailAuthentication()
            ->create();

        expect(array_keys(MultiFactorChallenge::make()->getEnabledProviders($user)))
            ->toBe([EmailAuthentication::make()->getId()]);
    });

    it('returns every provider that the user has enabled', function (): void {
        $user = User::factory()
            ->hasEmailAuthentication()
            ->hasAppAuthentication()
            ->create();

        expect(array_keys(MultiFactorChallenge::make()->getEnabledProviders($user)))
            ->toBe([EmailAuthentication::make()->getId(), AppAuthentication::make()->getId()]);
    });

    it('returns the first provider that the user has enabled', function (): void {
        $user = User::factory()
            ->hasAppAuthentication()
            ->create();

        expect(MultiFactorChallenge::make()->getFirstEnabledProvider($user))
            ->toBeInstanceOf(AppAuthentication::class);
    });

    it('returns `null` from `getFirstEnabledProvider()` when the user has no providers enabled', function (): void {
        $user = User::factory()->create();

        expect(MultiFactorChallenge::make()->getFirstEnabledProvider($user))
            ->toBeNull();
    });

    it('can check whether the user has any provider enabled', function (): void {
        $challenge = MultiFactorChallenge::make();

        expect($challenge->hasEnabledProviders(User::factory()->create()))
            ->toBeFalse()
            ->and($challenge->hasEnabledProviders(User::factory()->hasEmailAuthentication()->create()))
            ->toBeTrue();
    });
});

describe('`beforeChallenge()`', function (): void {
    it('runs the hook of the first enabled provider', function (): void {
        $user = User::factory()
            ->hasEmailAuthentication()
            ->create();

        MultiFactorChallenge::make()->beforeChallenge($user);

        Notification::assertSentTo($user, VerifyEmailAuthentication::class);
    });

    it('runs the hook of the given provider', function (): void {
        $user = User::factory()
            ->hasEmailAuthentication()
            ->hasAppAuthentication()
            ->create();

        $challenge = MultiFactorChallenge::make();
        $providers = $challenge->getEnabledProviders($user);

        $challenge->beforeChallenge($user, $providers[AppAuthentication::make()->getId()]);

        Notification::assertNothingSent();

        $challenge->beforeChallenge($user, $providers[EmailAuthentication::make()->getId()]);

        Notification::assertSentTo($user, VerifyEmailAuthentication::class);
    });

    it('does nothing when the user has no providers enabled', function (): void {
        MultiFactorChallenge::make()->beforeChallenge(User::factory()->create());

        Notification::assertNothingSent();
    });
});

describe('schema components', function (): void {
    it('does not return a provider picker when only one provider is enabled', function (): void {
        $user = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $challenge = MultiFactorChallenge::make();

        expect($challenge->getProviderPickerSchemaComponent($user))
            ->toBeNull()
            ->and($challenge->getSchemaComponents($user))
            ->toHaveCount(1)
            ->and($challenge->getChallengeSchemaComponents($user)[EmailAuthentication::make()->getId()])
            ->toBeInstanceOf(Group::class);
    });

    it('returns a provider picker when more than one provider is enabled', function (): void {
        $user = User::factory()
            ->hasEmailAuthentication()
            ->hasAppAuthentication()
            ->create();

        $challenge = MultiFactorChallenge::make();

        expect($challenge->getProviderPickerSchemaComponent($user))
            ->toBeInstanceOf(Section::class)
            ->and($challenge->getSchemaComponents($user))
            ->toHaveCount(3)
            ->and(array_keys($challenge->getChallengeSchemaComponents($user)))
            ->toBe([EmailAuthentication::make()->getId(), AppAuthentication::make()->getId()]);
    });

    it('returns no components when the user has no providers enabled', function (): void {
        expect(MultiFactorChallenge::make()->getSchemaComponents(User::factory()->create()))
            ->toBeEmpty();
    });

    it('only calls `isEnabled()` once per provider when building `getSchemaComponents()`', function (): void {
        $user = User::factory()->create();

        /** @var MockInterface&MultiFactorAuthenticationProvider $provider */
        $provider = Mockery::mock(MultiFactorAuthenticationProvider::class);
        $provider->shouldReceive('getId')->andReturn('custom');
        $provider->shouldReceive('isEnabled')->once()->with($user)->andReturnTrue();
        $provider->shouldReceive('getChallengeFormComponents')->once()->with($user)->andReturn([]);

        Filament::getCurrentPanel()->multiFactorAuthentication([$provider]);

        expect(MultiFactorChallenge::make()->getSchemaComponents($user))
            ->toHaveCount(1);
    });
});

describe('standalone challenge flow', function (): void {
    it('blocks an invalid code and accepts a valid code outside `Login`', function (): void {
        /** @var EmailAuthentication $emailAuthentication */
        $emailAuthentication = Filament::getMultiFactorAuthenticationProviders()[EmailAuthentication::make()->getId()];
        $emailAuthentication->generateCodesUsing(static fn (): string => '123456');

        $user = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $this->actingAs($user);

        livewire(MultiFactorChallengeBrowserTest::class)
            ->assertSet('isVerified', false)
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => '654321',
                ],
            ], 'multiFactorChallengeForm')
            ->call('verify')
            ->assertHasFormErrors([$emailAuthentication->getId() . '.code'], 'multiFactorChallengeForm')
            ->assertSet('isVerified', false)
            ->fillForm([
                $emailAuthentication->getId() => [
                    'code' => '123456',
                ],
            ], 'multiFactorChallengeForm')
            ->call('verify')
            ->assertHasNoFormErrors(form: 'multiFactorChallengeForm')
            ->assertSet('isVerified', true);
    });

    it('runs the selected provider hook when switching providers across separate layout containers', function (): void {
        $user = User::factory()
            ->hasEmailAuthentication()
            ->hasAppAuthentication()
            ->create();

        $this->actingAs($user);

        livewire(MultiFactorChallengeBrowserTest::class, [
            'shouldSeparateChallengeSchemaComponents' => true,
        ])
            ->assertSchemaStateSet([
                'provider' => EmailAuthentication::make()->getId(),
            ], 'multiFactorChallengeForm')
            ->set('data.provider', AppAuthentication::make()->getId())
            ->set('data.provider', EmailAuthentication::make()->getId());

        Notification::assertSentTimes(VerifyEmailAuthentication::class, 2);
    });

    it('fails closed when the user has no enabled providers', function (): void {
        $this->actingAs(User::factory()->create());

        livewire(MultiFactorChallengeBrowserTest::class)
            ->assertForbidden();
    });

    it('fails closed when all providers are disabled after the challenge is mounted', function (): void {
        $user = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $this->actingAs($user);

        $livewire = livewire(MultiFactorChallengeBrowserTest::class)
            ->assertSet('isVerified', false);

        $user->update(['has_email_authentication' => false]);

        $livewire
            ->call('verify')
            ->assertForbidden();
    });

    it('fails closed when the challenge is rate limited', function (): void {
        $user = User::factory()
            ->hasEmailAuthentication()
            ->create();

        $this->actingAs($user);

        $livewire = livewire(MultiFactorChallengeBrowserTest::class)
            ->assertSet('isVerified', false);

        $multiFactorChallenge = MultiFactorChallenge::make();

        foreach (range(1, $multiFactorChallenge->getMaxRateLimiterAttempts()) as $ignored) {
            $multiFactorChallenge->hitRateLimiter($user);
        }

        $livewire
            ->call('verify')
            ->assertStatus(429);
    });
});

describe('login flow', function (): void {
    it('challenges the user with the first provider that they have enabled', function (): void {
        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->hasAppAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->assertNotSet('userUndertakingMultiFactorAuthentication', null)
            ->assertNoRedirect()
            ->assertSchemaStateSet([
                'provider' => EmailAuthentication::make()->getId(),
            ], 'multiFactorChallengeForm');

        Notification::assertSentTo($userToAuthenticate, VerifyEmailAuthentication::class);

        $this->assertGuest();
    });

    it('can authenticate with another provider that the user has enabled', function (): void {
        /** @var AppAuthentication $appAuthentication */
        $appAuthentication = Filament::getMultiFactorAuthenticationProviders()[AppAuthentication::make()->getId()];

        $userToAuthenticate = User::factory()
            ->hasEmailAuthentication()
            ->hasAppAuthentication()
            ->create();

        livewire(Login::class)
            ->fillForm([
                'email' => $userToAuthenticate->email,
                'password' => 'password',
            ])
            ->call('authenticate')
            ->fillForm([
                'provider' => $appAuthentication->getId(),
                $appAuthentication->getId() => [
                    'code' => $appAuthentication->getCurrentCode($userToAuthenticate),
                ],
            ], 'multiFactorChallengeForm')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($userToAuthenticate);
    });
});

describe('rate limiting', function (): void {
    it('rate limits the challenge per user', function (): void {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $challenge = MultiFactorChallenge::make();

        expect($challenge->isRateLimited($user))->toBeFalse();

        foreach (range(1, $challenge->getMaxRateLimiterAttempts()) as $ignored) {
            $challenge->hitRateLimiter($user);
        }

        expect($challenge->isRateLimited($user))
            ->toBeTrue()
            ->and($challenge->getRateLimiterAvailableInSeconds($user))
            ->toBeGreaterThan(0)
            ->and($challenge->isRateLimited($anotherUser))
            ->toBeFalse();
    });

    it('scopes rate limits by authentication guard and user class', function (): void {
        $user = User::factory()->create();
        $userOfAnotherClass = new AlternateMultiFactorChallengeUser;
        $userOfAnotherClass->setAttribute($userOfAnotherClass->getAuthIdentifierName(), $user->getAuthIdentifier());

        $challenge = MultiFactorChallenge::make();

        foreach (range(1, $challenge->getMaxRateLimiterAttempts()) as $ignored) {
            $challenge->hitRateLimiter($user);
        }

        expect($challenge->isRateLimited($user))
            ->toBeTrue()
            ->and($challenge->isRateLimited($userOfAnotherClass))
            ->toBeFalse();

        Filament::getCurrentPanel()->authGuard('another-guard');

        expect($challenge->isRateLimited($user))->toBeFalse();
    });
});

it('has no accessibility issues outside `Login` in light mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()
            ->hasEmailAuthentication()
            ->hasAppAuthentication()
            ->create());

        visit(MultiFactorChallengeBrowserTest::getUrl(panel: 'required-multi-factor-authentication'))
            ->assertSee('Verify multi-factor authentication')
            ->assertSee('How would you like to verify?')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    });
});

it('has no accessibility issues outside `Login` in dark mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()
            ->hasEmailAuthentication()
            ->hasAppAuthentication()
            ->create());

        visit(MultiFactorChallengeBrowserTest::getUrl(panel: 'required-multi-factor-authentication'))
            ->inDarkMode()
            ->assertSee('Verify multi-factor authentication')
            ->assertSee('How would you like to verify?')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    });
});

class AlternateMultiFactorChallengeUser extends User {}
