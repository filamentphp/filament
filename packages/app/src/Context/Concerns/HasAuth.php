<?php

namespace Filament\Context\Concerns;

use Closure;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Auth\Login;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Filament\Pages\Auth\Register;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

trait HasAuth
{
    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $emailVerificationRouteAction = null;

    protected bool $isEmailVerificationRequired = false;

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $loginRouteAction = null;

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $registrationRouteAction = null;

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $requestPasswordResetRouteAction = null;

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $resetPasswordRouteAction = null;

    protected string $authGuard = 'web';

    /**
     * @param  string | Closure | array<class-string, string> | null  $promptAction
     */
    public function emailVerification(string | Closure | array | null $promptAction = EmailVerificationPrompt::class, bool $isRequired = true): static
    {
        $this->emailVerificationRouteAction = $promptAction;
        $this->requiresEmailVerification($isRequired);

        return $this;
    }

    public function requiresEmailVerification(bool $condition = true): static
    {
        $this->isEmailVerificationRequired = $condition;

        return $this;
    }

    /**
     * @param  string | Closure | array<class-string, string> | null  $action
     */
    public function login(string | Closure | array | null $action = Login::class): static
    {
        $this->loginRouteAction = $action;

        return $this;
    }

    /**
     * @param  string | Closure | array<class-string, string> | null  $requestAction
     * @param  string | Closure | array<class-string, string> | null  $resetAction
     */
    public function passwordReset(string | Closure | array | null $requestAction = RequestPasswordReset::class, string | Closure | array | null $resetAction = ResetPassword::class): static
    {
        $this->requestPasswordResetRouteAction = $requestAction;
        $this->resetPasswordRouteAction = $resetAction;

        return $this;
    }

    /**
     * @param  string | Closure | array<class-string, string> | null  $action
     */
    public function registration(string | Closure | array | null $action = Register::class): static
    {
        $this->registrationRouteAction = $action;

        return $this;
    }

    public function auth(): Guard
    {
        return auth()->guard($this->authGuard);
    }

    public function authGuard(string $guard): static
    {
        $this->authGuard = $guard;

        return $this;
    }

    public function isEmailVerificationRequired(): bool
    {
        return $this->isEmailVerificationRequired;
    }

    public function getEmailVerificationPromptUrl(): ?string
    {
        if (! $this->hasEmailVerification()) {
            return null;
        }

        return route($this->getEmailVerificationPromptRouteName());
    }

    public function getEmailVerificationPromptRouteName(): string
    {
        return "filament.{$this->getId()}.auth.email-verification.prompt";
    }

    public function getEmailVerifiedMiddleware(): string
    {
        return "verified:{$this->getEmailVerificationPromptRouteName()}";
    }

    public function getLoginUrl(): ?string
    {
        if (! $this->hasLogin()) {
            return null;
        }

        return route("filament.{$this->getId()}.auth.login");
    }

    public function getRegistrationUrl(): ?string
    {
        if (! $this->hasRegistration()) {
            return null;
        }

        return route("filament.{$this->getId()}.auth.register");
    }

    public function getRequestPasswordResetUrl(): ?string
    {
        if (! $this->hasPasswordReset()) {
            return null;
        }

        return route("filament.{$this->getId()}.auth.password-reset.request");
    }

    public function getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user): string
    {
        return URL::temporarySignedRoute(
            "filament.{$this->getId()}.auth.email-verification.verify",
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );
    }

    public function getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user): string
    {
        return URL::signedRoute("filament.{$this->getId()}.auth.password-reset.reset", [
            'email' => $user->getEmailForPasswordReset(),
            'token' => $token,
        ]);
    }

    public function getLogoutUrl(): string
    {
        return route("filament.{$this->getId()}.auth.logout");
    }

    /**
     * @return string | Closure | array<class-string, string> | null
     */
    public function getEmailVerificationPromptRouteAction(): string | Closure | array | null
    {
        return $this->emailVerificationRouteAction;
    }

    /**
     * @return string | Closure | array<class-string, string> | null
     */
    public function getLoginRouteAction(): string | Closure | array | null
    {
        return $this->loginRouteAction;
    }

    /**
     * @return string | Closure | array<class-string, string> | null
     */
    public function getRegistrationRouteAction(): string | Closure | array | null
    {
        return $this->registrationRouteAction;
    }

    /**
     * @return string | Closure | array<class-string, string> | null
     */
    public function getRequestPasswordResetRouteAction(): string | Closure | array | null
    {
        return $this->requestPasswordResetRouteAction;
    }

    /**
     * @return string | Closure | array<class-string, string> | null
     */
    public function getResetPasswordRouteAction(): string | Closure | array | null
    {
        return $this->resetPasswordRouteAction;
    }

    public function hasEmailVerification(): bool
    {
        return filled($this->getEmailVerificationPromptRouteAction());
    }

    public function hasLogin(): bool
    {
        return filled($this->getLoginRouteAction());
    }

    public function hasPasswordReset(): bool
    {
        return filled($this->getRequestPasswordResetRouteAction());
    }

    public function hasRegistration(): bool
    {
        return filled($this->getRegistrationRouteAction());
    }

    public function getAuthGuard(): ?string
    {
        return $this->authGuard;
    }
}
