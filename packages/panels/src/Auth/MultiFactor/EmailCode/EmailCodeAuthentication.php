<?php

namespace Filament\Auth\MultiFactor\EmailCode;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Auth\MultiFactor\Contracts\HasBeforeChallengeHook;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\EmailCode\Actions\DisableEmailCodeAuthenticationAction;
use Filament\Auth\MultiFactor\EmailCode\Actions\SetUpEmailCodeAuthenticationAction;
use Filament\Auth\MultiFactor\EmailCode\Contracts\HasEmailCodeAuthentication;
use Filament\Auth\MultiFactor\EmailCode\Notifications\VerifyEmailCodeAuthentication;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Text;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\RateLimiter;
use PragmaRX\Google2FAQRCode\Google2FA;

class EmailCodeAuthentication implements HasBeforeChallengeHook, MultiFactorAuthenticationProvider
{
    /**
     * 8 keys (respectively 4 minutes) past and future
     */
    protected int $codeWindow = 8;

    protected string $codeNotification = VerifyEmailCodeAuthentication::class;

    public function __construct(
        protected Google2FA $google2FA,
    ) {}

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
        return __('filament-panels::auth/multi-factor/email-code/provider.login_form.label');
    }

    public function isEnabled(Authenticatable $user): bool
    {
        if (! ($user instanceof HasEmailCodeAuthentication)) {
            throw new Exception('The user model must implement the [' . HasEmailCodeAuthentication::class . '] interface to use email code authentication.');
        }

        return $user->hasEmailCodeAuthentication();
    }

    public function sendCode(HasEmailCodeAuthentication $user, ?string $secret = null): void
    {
        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        $rateLimitingKey = 'filament_email_code_authentication.' . md5($secret ?? $this->getSecret($user));

        if (RateLimiter::tooManyAttempts($rateLimitingKey, maxAttempts: 1)) {
            return;
        }

        RateLimiter::hit($rateLimitingKey);

        $user->notify(app($this->getCodeNotification(), [
            'code' => $this->getCurrentCode($user, $secret),
            'codeWindow' => $this->getCodeWindow(),
        ]));
    }

    public function getCurrentCode(HasEmailCodeAuthentication $user, ?string $secret = null): string
    {
        return $this->google2FA->getCurrentOtp($secret ?? $this->getSecret($user));
    }

    public function getSecret(HasEmailCodeAuthentication $user): string
    {
        return $user->getEmailCodeAuthenticationSecret();
    }

    public function saveSecret(HasEmailCodeAuthentication $user, ?string $secret): void
    {
        $user->saveEmailCodeAuthenticationSecret($secret);
    }

    public function generateSecret(): string
    {
        return $this->google2FA->generateSecretKey();
    }

    public function verifyCode(string $code, ?string $secret = null): bool
    {
        /** @var HasEmailCodeAuthentication $user */
        $user = Filament::auth()->user();

        return $this->google2FA->verifyKey($secret ?? $this->getSecret($user), $code, $this->getCodeWindow());
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getManagementSchemaComponents(): array
    {
        $user = Filament::auth()->user();

        return [
            Actions::make($this->getActions())
                ->label(__('filament-panels::auth/multi-factor/email-code/provider.management_schema.actions.label'))
                ->belowContent(__('filament-panels::auth/multi-factor/email-code/provider.management_schema.actions.below_content'))
                ->afterLabel(fn (): Text => $this->isEnabled($user)
                    ? Text::make(__('filament-panels::auth/multi-factor/email-code/provider.management_schema.actions.messages.enabled'))
                        ->badge()
                        ->color('success')
                    : Text::make(__('filament-panels::auth/multi-factor/email-code/provider.management_schema.actions.messages.disabled'))
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
            SetUpEmailCodeAuthenticationAction::make($this)
                ->hidden(fn (): bool => $this->isEnabled($user)),
            DisableEmailCodeAuthenticationAction::make($this)
                ->visible(fn (): bool => $this->isEnabled($user)),
        ];
    }

    public function codeWindow(int $window): static
    {
        $this->codeWindow = $window;

        return $this;
    }

    public function getCodeWindow(): int
    {
        return $this->codeWindow;
    }

    public function beforeChallenge(Authenticatable $user): void
    {
        if (! ($user instanceof HasEmailCodeAuthentication)) {
            throw new Exception('The user model must implement the [' . HasEmailCodeAuthentication::class . '] interface to use email code authentication.');
        }

        $this->sendCode($user);
    }

    /**
     * @param  Authenticatable&HasEmailCodeAuthentication  $user
     */
    public function getChallengeFormComponents(Authenticatable $user): array
    {
        return [
            OneTimeCodeInput::make('code')
                ->label(__('filament-panels::auth/multi-factor/email-code/provider.login_form.code.label'))
                ->validationAttribute('code')
                ->belowContent(Action::make('resend')
                    ->label(__('filament-panels::auth/multi-factor/email-code/provider.login_form.code.actions.resend.label'))
                    ->link()
                    ->action(function () use ($user): void {
                        $this->sendCode($user);

                        Notification::make()
                            ->title(__('filament-panels::auth/multi-factor/email-code/provider.login_form.code.actions.resend.notifications.resent.title'))
                            ->success()
                            ->send();
                    }))
                ->required()
                ->rule(function () use ($user): Closure {
                    return function (string $attribute, $value, Closure $fail) use ($user): void {
                        if ($this->verifyCode($value, $this->getSecret($user))) {
                            return;
                        }

                        $fail(__('filament-panels::auth/multi-factor/email-code/provider.login_form.code.messages.invalid'));
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
