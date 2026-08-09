<?php

namespace Filament\Auth\MultiFactor;

use Filament\Auth\MultiFactor\Contracts\HasBeforeChallengeHook;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Facades\Filament;
use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\RateLimiter;

class MultiFactorChallenge
{
    protected int $maxRateLimiterAttempts = 5;

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<string, MultiFactorAuthenticationProvider>
     */
    public function getEnabledProviders(Authenticatable $user): array
    {
        return array_filter(
            Filament::getMultiFactorAuthenticationProviders(),
            fn (MultiFactorAuthenticationProvider $provider): bool => $provider->isEnabled($user),
        );
    }

    public function getFirstEnabledProvider(Authenticatable $user): ?MultiFactorAuthenticationProvider
    {
        foreach (Filament::getMultiFactorAuthenticationProviders() as $provider) {
            if ($provider->isEnabled($user)) {
                return $provider;
            }
        }

        return null;
    }

    public function hasEnabledProviders(Authenticatable $user): bool
    {
        return (bool) $this->getFirstEnabledProvider($user);
    }

    public function beforeChallenge(Authenticatable $user, ?MultiFactorAuthenticationProvider $provider = null): void
    {
        $provider ??= $this->getFirstEnabledProvider($user);

        if (! ($provider instanceof HasBeforeChallengeHook)) {
            return;
        }

        $provider->beforeChallenge($user);
    }

    /**
     * @return array<Component>
     */
    public function getSchemaComponents(Authenticatable $user): array
    {
        return [
            ...Arr::wrap($this->getProviderSchemaComponent($user)),
            ...$this->getChallengeSchemaComponents($user),
        ];
    }

    /**
     * The component that lets the user choose which of their enabled providers to
     * be challenged with. Returns `null` when there is nothing to choose between.
     */
    public function getProviderSchemaComponent(Authenticatable $user): ?Component
    {
        $enabledProviders = $this->getEnabledProviders($user);

        if (count($enabledProviders) <= 1) {
            return null;
        }

        return Section::make()
            ->compact()
            ->secondary()
            ->schema(fn (Section $section): array => [
                Radio::make('provider')
                    ->label(__('filament-panels::auth/pages/login.multi_factor.form.provider.label'))
                    ->options(array_map(
                        fn (MultiFactorAuthenticationProvider $provider): string => $provider->getLoginFormLabel(),
                        $enabledProviders,
                    ))
                    ->live()
                    ->afterStateUpdated(function (?string $state) use ($enabledProviders, $section, $user): void {
                        $provider = $enabledProviders[$state] ?? null;

                        if (! $provider) {
                            return;
                        }

                        $section
                            ->getContainer()
                            ->getComponent($provider->getId())
                            ->getChildSchema()
                            ->fill();

                        $this->beforeChallenge($user, $provider);
                    })
                    ->default(array_key_first($enabledProviders))
                    ->required()
                    ->markAsRequired(false),
            ]);
    }

    /**
     * @return array<Component>
     */
    public function getChallengeSchemaComponents(Authenticatable $user): array
    {
        $enabledProviders = $this->getEnabledProviders($user);

        return collect($enabledProviders)
            ->map(fn (MultiFactorAuthenticationProvider $provider): Component => Group::make($provider->getChallengeFormComponents($user))
                ->statePath($provider->getId())
                ->when(
                    count($enabledProviders) > 1,
                    fn (Group $group) => $group->visible(fn (Get $get): bool => $get('provider') === $provider->getId()),
                ))
            ->all();
    }

    public function isRateLimited(Authenticatable $user): bool
    {
        return RateLimiter::tooManyAttempts($this->getRateLimiterKey($user), $this->getMaxRateLimiterAttempts());
    }

    public function hitRateLimiter(Authenticatable $user): void
    {
        RateLimiter::hit($this->getRateLimiterKey($user));
    }

    public function getRateLimiterAvailableInSeconds(Authenticatable $user): int
    {
        return RateLimiter::availableIn($this->getRateLimiterKey($user));
    }

    protected function getRateLimiterKey(Authenticatable $user): string
    {
        return "filament-multi-factor-challenge:{$user->getAuthIdentifier()}";
    }

    public function getMaxRateLimiterAttempts(): int
    {
        return $this->maxRateLimiterAttempts;
    }
}
