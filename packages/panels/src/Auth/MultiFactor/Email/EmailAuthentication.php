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

        $rateLimitingKey = "filament_email_authentication.{$user->getKey()}";

        if (RateLimiter::tooManyAttempts($rateLimitingKey, maxAttempts: 2)) {
            return false;
        }

        RateLimiter::hit($rateLimitingKey);

        $code = $this->generateCode();
        $codeExpiryMinutes = $this->getCodeExpiryMinutes();

        session()->put('filament_email_authentication_code', Hash::make($code));
        session()->put('filament_email_authentication_code_expires_at', now()->addMinutes($codeExpiryMinutes));

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

    public function verifyCode(string $code): bool
    {
        $codeHash = session('filament_email_authentication_code');
        $codeExpiresAt = session('filament_email_authentication_code_expires_at');

        if (
            blank($codeHash)
            || blank($codeExpiresAt)
            || (! Hash::check($code, $codeHash))
            || now()->greaterThan($codeExpiresAt)
        ) {
            return false;
        }

        session()->forget('filament_email_authentication_code');
        session()->forget('filament_email_authentication_code_expires_at');

        return true;
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
                ->rule(function (): Closure {
                    return function (string $attribute, $value, Closure $fail): void {
                        if ($this->verifyCode($value)) {
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
