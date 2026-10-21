<?php

namespace Filament\Auth\MultiFactor\Email;

use Carbon\CarbonImmutable;
use Closure;
use DateTimeInterface;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Auth\MultiFactor\Concerns\HasCacheStore;
use Filament\Auth\MultiFactor\Contracts\HasBeforeChallengeHook;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\Email\Actions\DisableEmailAuthenticationAction;
use Filament\Auth\MultiFactor\Email\Actions\SetUpEmailAuthenticationAction;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\MultiFactor\Email\Notifications\VerifyEmailAuthentication;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Text;
use Illuminate\Cache\Lock;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use LogicException;
use SensitiveParameter;

use function Filament\Support\encode_key_segments;

class EmailAuthentication implements HasBeforeChallengeHook, MultiFactorAuthenticationProvider
{
    use HasCacheStore;

    protected int $codeExpiryMinutes = 4;

    protected string $codeNotification = VerifyEmailAuthentication::class;

    protected ?Closure $generateCodesUsing = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'email_code';
    }

    public function getLoginFormLabel(): string
    {
        return __('filament-panels::auth/multi-factor/email/provider.login_form.label');
    }

    public function isEnabled(Authenticatable $user): bool
    {
        if (! ($user instanceof HasEmailAuthentication)) {
            throw new LogicException('The user model must implement the [' . HasEmailAuthentication::class . '] interface to use email authentication.');
        }

        return $user->hasEmailAuthentication();
    }

    public function sendCode(HasEmailAuthentication $user): bool
    {
        if (! ($user instanceof Model)) {
            throw new LogicException('The [' . $user::class . '] class must be an instance of [' . Model::class . '] to use email authentication.');
        }

        if (! ($user instanceof Authenticatable)) {
            throw new LogicException('The [' . $user::class . '] class must implement [' . Authenticatable::class . '] to use email authentication.');
        }

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new LogicException("Model [{$userClass}] does not have a [notify()] method.");
        }

        $rateLimitingKey = 'filament:auth:email_authentication:rate_limit:' . Filament::getUserScopedAuthIdentifier($user);

        if (RateLimiter::tooManyAttempts($rateLimitingKey, maxAttempts: 2)) {
            return false;
        }

        RateLimiter::hit($rateLimitingKey);

        $code = $this->generateCode();
        $codeExpiryMinutes = $this->getCodeExpiryMinutes();
        $codeHash = Hash::make($code);
        $codeExpiresAt = now()->addMinutes($codeExpiryMinutes);
        $codeCacheKey = $this->getCodeCacheKey($user);
        $legacyCodeCacheKey = $this->getLegacyCodeCacheKey($user);
        $cache = $this->getCacheRepository();

        $wasStored = $this->executeWithCacheLock(
            $cache,
            "{$codeCacheKey}:lock",
            function (?Lock $lock, Repository $cache) use ($user, $codeCacheKey, $legacyCodeCacheKey, $codeExpiresAt, $codeHash): bool {
                if (($lock !== null) && (! $lock->isOwnedByCurrentProcess())) {
                    return false;
                }

                if (! $cache->put($codeCacheKey, [
                    'hash' => $codeHash,
                    'expiresAt' => $codeExpiresAt,
                ], $codeExpiresAt)) {
                    return false;
                }

                if (! $this->forgetLegacySessionCode($cache, $codeCacheKey, $user)) {
                    return false;
                }

                return (! $cache->has($legacyCodeCacheKey))
                    || $cache->forget($legacyCodeCacheKey)
                    || ($cache->get($legacyCodeCacheKey) === null);
            },
        );

        if (! $wasStored) {
            return false;
        }

        $user->notify(app($this->getCodeNotification(), [
            'code' => $code,
            'codeExpiryMinutes' => $codeExpiryMinutes,
        ]));

        return true;
    }

    public function enableEmailAuthentication(HasEmailAuthentication $user): void
    {
        $user->toggleEmailAuthentication(true);
    }

    public function generateCodesUsing(?Closure $callback): static
    {
        $this->generateCodesUsing = $callback;

        return $this;
    }

    public function generateCode(): string
    {
        if ($this->generateCodesUsing) {
            return ($this->generateCodesUsing)();
        }

        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Codes are stored against the user they were issued for, so that a code
     * obtained for one account cannot satisfy a challenge for another. When no
     * user is passed, the authenticated user is verified against, which is only
     * correct outside of the login flow.
     */
    public function verifyCode(#[SensitiveParameter] string $code, ?HasEmailAuthentication $user = null): bool
    {
        $user ??= Filament::auth()->user();

        if (! ($user instanceof HasEmailAuthentication)) {
            return false;
        }

        if (! ($user instanceof Model)) {
            return false;
        }

        if (! ($user instanceof Authenticatable)) {
            return false;
        }

        $cache = $this->getCacheRepository();
        $codeCacheKey = $this->getCodeCacheKey($user);
        $legacyCodeCacheKey = $this->getLegacyCodeCacheKey($user);

        return $this->executeWithCacheLock($cache, "{$codeCacheKey}:lock", function (?Lock $lock, Repository $cache) use ($user, $code, $codeCacheKey, $legacyCodeCacheKey): bool {
            $activeCodeCacheKey = $cache->has($codeCacheKey) ? $codeCacheKey : ($cache->has($legacyCodeCacheKey) ? $legacyCodeCacheKey : null);

            if (($activeCodeCacheKey === null) && ($cache->has("{$codeCacheKey}:legacy_session") || $cache->has("{$codeCacheKey}:legacy_session_expires_at"))) {
                return false;
            }

            $activeCode = ($activeCodeCacheKey !== null) ? $cache->get($activeCodeCacheKey) : [
                'hash' => session($this->getLegacyCodeSessionKey($user)),
                'expiresAt' => session($this->getLegacyCodeExpirySessionKey($user)),
            ];
            $activeCodeHash = is_array($activeCode) ? ($activeCode['hash'] ?? null) : null;
            $activeCodeExpiresAt = is_array($activeCode) ? ($activeCode['expiresAt'] ?? null) : null;

            if (
                (! is_string($activeCodeHash))
                || (! ($activeCodeExpiresAt instanceof DateTimeInterface))
                || $this->isCodeExpired($activeCodeExpiresAt)
                || (! Hash::check($code, $activeCodeHash))
            ) {
                return false;
            }

            $legacySessionCodeCacheKey = $this->getLegacySessionCodeCacheKey($activeCodeHash);

            if (($activeCodeCacheKey === null) && $cache->has($legacySessionCodeCacheKey)) {
                return false;
            }

            $currentCode = ($activeCodeCacheKey !== null) ? $cache->get($activeCodeCacheKey) : [
                'hash' => session($this->getLegacyCodeSessionKey($user)),
            ];
            $currentCodeHash = is_array($currentCode) ? ($currentCode['hash'] ?? null) : null;

            if (
                (! is_string($currentCodeHash))
                || (! hash_equals($activeCodeHash, $currentCodeHash))
                || $this->isCodeExpired($activeCodeExpiresAt)
                || (($activeCodeCacheKey !== $codeCacheKey) && $cache->has($codeCacheKey))
                || (($activeCodeCacheKey === null) && $cache->has($legacyCodeCacheKey))
                || (($lock !== null) && (! $lock->isOwnedByCurrentProcess()))
            ) {
                return false;
            }

            if ($activeCodeCacheKey === null) {
                // A cache marker prevents stale session writes from restoring a consumed legacy code.
                if (! $cache->add($legacySessionCodeCacheKey, true, CarbonImmutable::instance($activeCodeExpiresAt)->addSecond())) {
                    return false;
                }

                return $this->forgetLegacySessionCode($cache, $codeCacheKey, $user);
            }

            if (! $this->forgetLegacySessionCode($cache, $codeCacheKey, $user)) {
                return false;
            }

            if ((($activeCodeCacheKey === $legacyCodeCacheKey) || $cache->has($legacyCodeCacheKey)) && (! $cache->forget($legacyCodeCacheKey))) {
                return false;
            }

            return ($activeCodeCacheKey === $legacyCodeCacheKey) || $cache->forget($codeCacheKey);
        });
    }

    /**
     * @phpstan-impure
     */
    protected function isCodeExpired(DateTimeInterface $expiresAt): bool
    {
        return now()->greaterThan($expiresAt);
    }

    /**
     * @param  Authenticatable&Model&HasEmailAuthentication  $user
     */
    protected function getCodeCacheKey(HasEmailAuthentication $user): string
    {
        return 'filament:auth:email_authentication:code:' . hash(
            'sha256',
            encode_key_segments(Filament::getUserScopedAuthIdentifier($user), session()->getId()),
        );
    }

    /**
     * @param  Authenticatable&Model&HasEmailAuthentication  $user
     */
    protected function getLegacyCodeCacheKey(HasEmailAuthentication $user): string
    {
        return 'filament.email_authentication_codes.' . hash(
            'sha256',
            Filament::getUserScopedAuthIdentifier($user) . '|' . session()->getId(),
        );
    }

    /**
     * @param  Model&HasEmailAuthentication  $user
     */
    protected function getLegacyCodeSessionKey(HasEmailAuthentication $user): string
    {
        return 'filament_email_authentication_code:' . $user->getKey();
    }

    /**
     * @param  Model&HasEmailAuthentication  $user
     */
    protected function getLegacyCodeExpirySessionKey(HasEmailAuthentication $user): string
    {
        return 'filament_email_authentication_code_expires_at:' . $user->getKey();
    }

    /**
     * @param  Model&HasEmailAuthentication  $user
     */
    protected function forgetLegacySessionCode(Repository $cache, string $codeCacheKey, HasEmailAuthentication $user): bool
    {
        $codeHash = session($this->getLegacyCodeSessionKey($user));
        $expiresAt = session($this->getLegacyCodeExpirySessionKey($user));

        if (is_string($codeHash) && ($expiresAt instanceof DateTimeInterface) && (! $this->isCodeExpired($expiresAt))) {
            // Cache stores can round expiry down to whole seconds. Do not extend the code's validity.
            $markerExpiresAt = CarbonImmutable::instance($expiresAt)->addSecond();
            $scopeCacheKey = "{$codeCacheKey}:legacy_session_expires_at";
            $previousMarkerExpiresAt = $cache->get($scopeCacheKey);

            if (($previousMarkerExpiresAt instanceof DateTimeInterface) && $markerExpiresAt->lessThan($previousMarkerExpiresAt)) {
                $markerExpiresAt = CarbonImmutable::instance($previousMarkerExpiresAt);
            }

            // Keep older, unseen session snapshots invalidated without shortening an existing barrier.
            if (! $cache->put($scopeCacheKey, $markerExpiresAt, $markerExpiresAt)) {
                return false;
            }

            $cacheKey = $this->getLegacySessionCodeCacheKey($codeHash);

            if ((! $cache->add($cacheKey, true, CarbonImmutable::instance($expiresAt)->addSecond())) && (! $cache->has($cacheKey))) {
                return false;
            }
        }

        session()->forget([$this->getLegacyCodeSessionKey($user), $this->getLegacyCodeExpirySessionKey($user)]);

        return true;
    }

    protected function getLegacySessionCodeCacheKey(#[SensitiveParameter] string $codeHash): string
    {
        return 'filament:auth:email_authentication:legacy_session:' . hash('sha256', $codeHash);
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getManagementSchemaComponents(): array
    {
        $user = Filament::auth()->user();

        return [
            Actions::make($this->getActions())
                ->label(__('filament-panels::auth/multi-factor/email/provider.management_schema.actions.label'))
                ->belowContent(__('filament-panels::auth/multi-factor/email/provider.management_schema.actions.below_content'))
                ->afterLabel(fn (): Text => $this->isEnabled($user)
                    ? Text::make(__('filament-panels::auth/multi-factor/email/provider.management_schema.actions.messages.enabled'))
                        ->badge()
                        ->color('success')
                    : Text::make(__('filament-panels::auth/multi-factor/email/provider.management_schema.actions.messages.disabled'))
                        ->badge()),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        $user = Filament::auth()->user();

        return [
            SetUpEmailAuthenticationAction::make($this)
                ->hidden(fn (): bool => $this->isEnabled($user)),
            DisableEmailAuthenticationAction::make($this)
                ->visible(fn (): bool => $this->isEnabled($user)),
        ];
    }

    public function codeExpiryMinutes(int $minutes): static
    {
        $this->codeExpiryMinutes = $minutes;

        return $this;
    }

    public function getCodeExpiryMinutes(): int
    {
        return $this->codeExpiryMinutes;
    }

    public function beforeChallenge(Authenticatable $user): void
    {
        if (! ($user instanceof HasEmailAuthentication)) {
            throw new LogicException('The user model must implement the [' . HasEmailAuthentication::class . '] interface to use email authentication.');
        }

        $this->sendCode($user);
    }

    /**
     * @param  Authenticatable&HasEmailAuthentication  $user
     */
    public function getChallengeFormComponents(Authenticatable $user): array
    {
        return [
            OneTimeCodeInput::make('code')
                ->label(__('filament-panels::auth/multi-factor/email/provider.login_form.code.label'))
                ->submitOnCompletion()
                ->validationAttribute('code')
                ->belowContent(Action::make('resend')
                    ->label(__('filament-panels::auth/multi-factor/email/provider.login_form.code.actions.resend.label'))
                    ->link()
                    ->action(function () use ($user): void {
                        if (! $this->sendCode($user)) {
                            Notification::make()
                                ->title(__('filament-panels::auth/multi-factor/email/provider.login_form.code.actions.resend.notifications.throttled.title'))
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title(__('filament-panels::auth/multi-factor/email/provider.login_form.code.actions.resend.notifications.resent.title'))
                            ->success()
                            ->send();
                    }))
                ->required()
                ->rule(function () use ($user): Closure {
                    return function (string $attribute, #[SensitiveParameter] $value, Closure $fail) use ($user): void {
                        if (is_string($value) && $this->verifyCode($value, $user)) {
                            return;
                        }

                        $fail(__('filament-panels::auth/multi-factor/email/provider.login_form.code.messages.invalid'));
                    };
                }),
        ];
    }

    public function codeNotification(string $notification): static
    {
        $this->codeNotification = $notification;

        return $this;
    }

    public function getCodeNotification(): string
    {
        return $this->codeNotification;
    }
}
