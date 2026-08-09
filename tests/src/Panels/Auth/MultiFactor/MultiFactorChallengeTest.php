<?php

use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Auth\MultiFactor\MultiFactorChallenge;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
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

        expect($challenge->getProviderSchemaComponent($user))
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

        expect($challenge->getProviderSchemaComponent($user))
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

        RateLimiter::clear("filament-multi-factor-challenge:{$user->getAuthIdentifier()}");

        expect($challenge->isRateLimited($user))->toBeFalse();
    });
});
