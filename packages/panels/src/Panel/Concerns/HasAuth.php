<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Auth\MultiFactor\Http\Middleware\EnsureMultiFactorAuthenticationIsEnabled;
use Filament\Auth\MultiFactor\Pages\SetUpRequiredMultiFactorAuthentication;
use Filament\Auth\Pages\EditProfile;
use Filament\Auth\Pages\EmailVerification\EmailVerificationPrompt;
use Filament\Auth\Pages\Login;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Auth\Pages\PasswordReset\ResetPassword;
use Filament\Auth\Pages\Register;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

trait HasAuth
{
    protected string | Closure $emailVerifiedMiddlewareName = 'verified';

    protected string | Closure $multiFactorAuthenticationRequiredMiddlewareName = EnsureMultiFactorAuthenticationIsEnabled::class;

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $emailVerificationRouteAction = null;

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $setUpRequiredMultiFactorAuthenticationRouteAction = null;

    protected bool | Closure $hasEmailChangeVerification = false;

    protected string $emailVerificationPromptRouteSlug = 'prompt';

    protected string $setUpRequiredMultiFactorAuthenticationRouteSlug = 'set-up';

    protected string $emailVerificationRouteSlug = 'verify';

    protected string $emailChangeVerificationRouteSlug = 'verify';

    protected string $emailVerificationRoutePrefix = 'email-verification';

    protected string $multiFactorAuthenticationRoutePrefix = 'multi-factor-authentication';

    protected string $emailChangeVerificationRoutePrefix = 'email-change-verification';

    protected bool | Closure $isEmailVerificationRequired = false;

    protected bool | Closure $isMultiFactorAuthenticationRequired = false;

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $loginRouteAction = null;

    protected string $loginRouteSlug = 'login';

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $registrationRouteAction = null;

    protected string $registrationRouteSlug = 'register';

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $requestPasswordResetRouteAction = null;

    protected string $requestPasswordResetRouteSlug = 'request';

    /**
     * @var string | Closure | array<class-string, string> | null
     */
    protected string | Closure | array | null $resetPasswordRouteAction = null;

    protected string $resetPasswordRouteSlug = 'reset';

    protected string $resetPasswordRoutePrefix = 'password-reset';

    protected ?string $profilePage = null;

    protected bool $isProfilePageSimple = true;

    protected string $authGuard = 'web';

    protected ?string $authPasswordBroker = null;

    protected bool | Closure $arePasswordsRevealable = true;

    /**
     * @var array<MultiFactorAuthenticationProvider> | MultiFactorAuthenticationProvider | Closure
     */
    protected array | MultiFactorAuthenticationProvider | Closure $multiFactorAuthenticationProviders = [];

    protected bool | Closure $isAuthorizationStrict = false;

    /**
     * @param  string | Closure | array<class-string, string> | null  $promptAction
     */
    public function emailVerification(string | Closure | array | null $promptAction = EmailVerificationPrompt::class, bool | Closure $isRequired = true): static
    {
        $this->emailVerificationRouteAction = $promptAction;
        $this->requiresEmailVerification($isRequired);

        return $this;
    }

    public function emailChangeVerification(bool | Closure $condition = true): static
    {
        $this->hasEmailChangeVerification = $condition;

        return $this;
    }

    public function emailVerificationPromptRouteSlug(string $slug): static
    {
        $this->emailVerificationPromptRouteSlug = $slug;

        return $this;
    }

    public function emailVerificationRouteSlug(string $slug): static
    {
        $this->emailVerificationRouteSlug = $slug;

        return $this;
    }

    public function emailChangeVerificationRouteSlug(string $slug): static
    {
        $this->emailChangeVerificationRouteSlug = $slug;

        return $this;
    }

    public function emailVerificationRoutePrefix(string $prefix): static
    {
        $this->emailVerificationRoutePrefix = $prefix;

        return $this;
    }

    public function setUpRequiredMultiFactorAuthenticationRouteSlug(string $slug): static
    {
        $this->setUpRequiredMultiFactorAuthenticationRouteSlug = $slug;

        return $this;
    }

    public function multiFactorAuthenticationRoutePrefix(string $prefix): static
    {
        $this->multiFactorAuthenticationRoutePrefix = $prefix;

        return $this;
    }

    public function emailChangeVerificationRoutePrefix(string $prefix): static
    {
        $this->emailChangeVerificationRoutePrefix = $prefix;

        return $this;
    }

    public function emailVerifiedMiddlewareName(string | Closure $name): static
    {
        $this->emailVerifiedMiddlewareName = $name;

        return $this;
    }

    public function multiFactorAuthenticationRequiredMiddlewareName(string | Closure $name): static
    {
        $this->multiFactorAuthenticationRequiredMiddlewareName = $name;

        return $this;
    }

    public function requiresEmailVerification(bool | Closure $condition = true): static
    {
        $this->isEmailVerificationRequired = $condition;

        return $this;
    }

    public function requiresMultiFactorAuthentication(bool | Closure $condition = true): static
    {
        $this->isMultiFactorAuthenticationRequired = $condition;

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

    public function loginRouteSlug(string $slug): static
    {
        $this->loginRouteSlug = $slug;

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

    public function passwordResetRequestRouteSlug(string $slug): static
    {
        $this->requestPasswordResetRouteSlug = $slug;

        return $this;
    }

    public function passwordResetRouteSlug(string $slug): static
    {
        $this->resetPasswordRouteSlug = $slug;

        return $this;
    }

    public function passwordResetRoutePrefix(string $prefix): static
    {
        $this->resetPasswordRoutePrefix = $prefix;

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

    public function registrationRouteSlug(string $slug): static
    {
        $this->registrationRouteSlug = $slug;

        return $this;
    }

    public function profile(?string $page = EditProfile::class, bool $isSimple = true): static
    {
        $this->profilePage = $page;
        $this->simpleProfilePage($isSimple);

        return $this;
    }

    public function simpleProfilePage(bool $condition = true): static
    {
        $this->isProfilePageSimple = $condition;

        return $this;
    }

    public function auth(): Guard
    {
        return auth()->guard($this->getAuthGuard());
    }

    public function authGuard(string $guard): static
    {
        $this->authGuard = $guard;

        return $this;
    }

    public function authPasswordBroker(?string $broker = null): static
    {
        $this->authPasswordBroker = $broker;

        return $this;
    }

    public function isEmailVerificationRequired(): bool
    {
        return (bool) $this->evaluate($this->isEmailVerificationRequired);
    }

    public function hasMultiFactorAuthentication(): bool
    {
        return ! empty($this->getMultiFactorAuthenticationProviders());
    }

    public function isMultiFactorAuthenticationRequired(): bool
    {
        return (bool) $this->evaluate($this->isMultiFactorAuthenticationRequired);
    }

    public function hasProfile(): bool
    {
        return filled($this->getProfilePage());
    }

    public function getProfilePage(): ?string
    {
        return $this->profilePage;
    }

    public function isProfilePageSimple(): bool
    {
        return $this->isProfilePageSimple;
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getEmailVerificationPromptUrl(array $parameters = []): ?string
    {
        if (! $this->hasEmailVerification()) {
            return null;
        }

        return route($this->getEmailVerificationPromptRouteName(), $parameters);
    }

    public function getEmailVerificationPromptRouteName(): string
    {
        return $this->generateRouteName('auth.email-verification.prompt');
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getSetUpRequiredMultiFactorAuthenticationUrl(array $parameters = []): ?string
    {
        if (! $this->hasMultiFactorAuthentication()) {
            return null;
        }

        return route($this->getSetUpRequiredMultiFactorAuthenticationRouteName(), $parameters);
    }

    public function getSetUpRequiredMultiFactorAuthenticationRouteName(): string
    {
        return $this->generateRouteName('auth.multi-factor-authentication.set-up-required');
    }

    public function getEmailVerifiedMiddleware(): string
    {
        return "{$this->getEmailVerifiedMiddlewareName()}:{$this->getEmailVerificationPromptRouteName()}";
    }

    public function getMultiFactorAuthenticationRequiredMiddleware(): string
    {
        return $this->getMultiFactorAuthenticationRequiredMiddlewareName();
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getLoginUrl(array $parameters = []): ?string
    {
        if (! $this->hasLogin()) {
            return null;
        }

        return $this->route('auth.login', $parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getRegistrationUrl(array $parameters = []): ?string
    {
        if (! $this->hasRegistration()) {
            return null;
        }

        return $this->route('auth.register', $parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getRequestPasswordResetUrl(array $parameters = []): ?string
    {
        if (! $this->hasPasswordReset()) {
            return null;
        }

        return $this->route('auth.password-reset.request', $parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user, array $parameters = []): string
    {
        return URL::temporarySignedRoute(
            $this->generateRouteName('auth.email-verification.verify'),
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
                ...$parameters,
            ],
        );
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getVerifyEmailChangeUrl(MustVerifyEmail | Model | Authenticatable $user, string $newEmail, array $parameters = []): string
    {
        return URL::temporarySignedRoute(
            $this->generateRouteName('auth.email-change-verification.verify'),
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'email' => encrypt($newEmail),
                ...$parameters,
            ],
        );
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getBlockEmailChangeVerificationUrl(MustVerifyEmail | Model | Authenticatable $user, string $newEmail, string $verificationSignature, array $parameters = []): string
    {
        return URL::temporarySignedRoute(
            $this->generateRouteName('auth.email-change-verification.block-verification'),
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'email' => encrypt($newEmail),
                'verificationSignature' => $verificationSignature,
                ...$parameters,
            ],
        );
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user, array $parameters = []): string
    {
        return URL::signedRoute(
            $this->generateRouteName('auth.password-reset.reset'),
            [
                'email' => $user->getEmailForPasswordReset(),
                'token' => $token,
                ...$parameters,
            ],
        );
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getProfileUrl(array $parameters = []): ?string
    {
        if (! $this->hasProfile()) {
            return null;
        }

        return $this->route('auth.profile', $parameters);
    }

    /**
     * @param  array<mixed>  $parameters
     */
    public function getLogoutUrl(array $parameters = []): string
    {
        return $this->route('auth.logout', $parameters);
    }

    public function getEmailVerifiedMiddlewareName(): string
    {
        return $this->evaluate($this->emailVerifiedMiddlewareName);
    }

    public function getMultiFactorAuthenticationRequiredMiddlewareName(): string
    {
        return $this->evaluate($this->multiFactorAuthenticationRequiredMiddlewareName);
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
    public function getSetUpRequiredMultiFactorAuthenticationRouteAction(): string | Closure | array | null
    {
        return $this->setUpRequiredMultiFactorAuthenticationRouteAction;
    }

    public function getEmailVerificationPromptRouteSlug(): string
    {
        return Str::start($this->emailVerificationPromptRouteSlug, '/');
    }

    public function getSetUpRequiredMultiFactorAuthenticationRouteSlug(): string
    {
        return Str::start($this->setUpRequiredMultiFactorAuthenticationRouteSlug, '/');
    }

    public function getEmailVerificationRouteSlug(string $suffix): string
    {
        return Str::start($this->emailVerificationRouteSlug, '/') . $suffix;
    }

    public function getEmailChangeVerificationRouteSlug(string $suffix): string
    {
        return Str::start($this->emailChangeVerificationRouteSlug, '/') . $suffix;
    }

    public function getEmailVerificationRoutePrefix(): string
    {
        return Str::start($this->emailVerificationRoutePrefix, '/');
    }

    public function getMultiFactorAuthenticationRoutePrefix(): string
    {
        return Str::start($this->multiFactorAuthenticationRoutePrefix, '/');
    }

    public function getEmailChangeVerificationRoutePrefix(): string
    {
        return Str::start($this->emailChangeVerificationRoutePrefix, '/');
    }

    /**
     * @return string | Closure | array<class-string, string> | null
     */
    public function getLoginRouteAction(): string | Closure | array | null
    {
        return $this->loginRouteAction;
    }

    public function getLoginRouteSlug(): string
    {
        return Str::start($this->loginRouteSlug, '/');
    }

    /**
     * @return string | Closure | array<class-string, string> | null
     */
    public function getRegistrationRouteAction(): string | Closure | array | null
    {
        return $this->registrationRouteAction;
    }

    public function getRegistrationRouteSlug(): string
    {
        return Str::start($this->registrationRouteSlug, '/');
    }

    /**
     * @return string | Closure | array<class-string, string> | null
     */
    public function getRequestPasswordResetRouteAction(): string | Closure | array | null
    {
        return $this->requestPasswordResetRouteAction;
    }

    public function getRequestPasswordResetRouteSlug(): string
    {
        return Str::start($this->requestPasswordResetRouteSlug, '/');
    }

    /**
     * @return string | Closure | array<class-string, string> | null
     */
    public function getResetPasswordRouteAction(): string | Closure | array | null
    {
        return $this->resetPasswordRouteAction;
    }

    public function getResetPasswordRouteSlug(): string
    {
        return Str::start($this->resetPasswordRouteSlug, '/');
    }

    public function getResetPasswordRoutePrefix(): string
    {
        return Str::start($this->resetPasswordRoutePrefix, '/');
    }

    public function hasEmailChangeVerification(): bool
    {
        return (bool) $this->evaluate($this->hasEmailChangeVerification);
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

    public function getAuthGuard(): string
    {
        return $this->authGuard;
    }

    public function getAuthPasswordBroker(): ?string
    {
        return $this->authPasswordBroker;
    }

    public function revealablePasswords(bool | Closure $condition = true): static
    {
        $this->arePasswordsRevealable = $condition;

        return $this;
    }

    public function arePasswordsRevealable(): bool
    {
        return (bool) $this->evaluate($this->arePasswordsRevealable);
    }

    /**
     * @param  array<MultiFactorAuthenticationProvider> | MultiFactorAuthenticationProvider | Closure  $providers
     * @param  string | Closure | array<class-string, string> | null  $setUpRequiredAction
     */
    public function multiFactorAuthentication(array | MultiFactorAuthenticationProvider | Closure $providers, string | Closure | array | null $setUpRequiredAction = SetUpRequiredMultiFactorAuthentication::class, bool | Closure $isRequired = false): static
    {
        $this->multiFactorAuthenticationProviders = $providers;
        $this->setUpRequiredMultiFactorAuthenticationRouteAction = $setUpRequiredAction;
        $this->requiresMultiFactorAuthentication($isRequired);

        return $this;
    }

    /**
     * @return array<string, MultiFactorAuthenticationProvider>
     */
    public function getMultiFactorAuthenticationProviders(): array
    {
        $providers = $this->evaluate($this->multiFactorAuthenticationProviders);

        if (empty($providers)) {
            return [];
        }

        return Collection::wrap($providers)
            ->mapWithKeys(fn (MultiFactorAuthenticationProvider $provider): array => [$provider->getId() => $provider])
            ->all();
    }

    public function strictAuthorization(bool | Closure $condition = true): static
    {
        $this->isAuthorizationStrict = $condition;

        return $this;
    }

    public function isAuthorizationStrict(): bool
    {
        return (bool) $this->evaluate($this->isAuthorizationStrict);
    }
}
