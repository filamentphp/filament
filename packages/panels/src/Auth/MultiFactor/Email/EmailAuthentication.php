<?php

namespace Filament\Auth\MultiFactor\Email;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use LogicException;
use SensitiveParameter;

class EmailAuthentication implements HasBeforeChallengeHook, MultiFactorAuthenticationProvider
{
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

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new LogicException("Model [{$userClass}] does not have a [notify()] method.");
        }

        $rateLimitingKey = "filament-email-authentication:{$user->getKey()}";

        if (RateLimiter::tooManyAttempts($rateLimitingKey, maxAttempts: 2)) {
            return false;
        }

        RateLimiter::hit($rateLimitingKey);

        $code = $this->generateCode();
        $codeExpiryMinutes = $this->getCodeExpiryMinutes();

        session()->put($this->getCodeSessionKey($user), Hash::make($code));
        session()->put($this->getCodeExpirySessionKey($user), now()->addMinutes($codeExpiryMinutes));

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

        $codeHash = session($this->getCodeSessionKey($user));
        $codeExpiresAt = session($this->getCodeExpirySessionKey($user));

        if (
            blank($codeHash)
            || blank($codeExpiresAt)
            || (! Hash::check($code, $codeHash))
            || now()->greaterThan($codeExpiresAt)
        ) {
            return false;
        }

        session()->forget($this->getCodeSessionKey($user));
        session()->forget($this->getCodeExpirySessionKey($user));

        return true;
    }

    /**
     * @param  Model&HasEmailAuthentication  $user
     */
    protected function getCodeSessionKey(HasEmailAuthentication $user): string
    {
        return 'filament_email_authentication_code:' . $user->getKey();
    }

    /**
     * @param  Model&HasEmailAuthentication  $user
     */
    protected function getCodeExpirySessionKey(HasEmailAuthentication $user): string
    {
        return 'filament_email_authentication_code_expires_at:' . $user->getKey();
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
