<?php

namespace Filament\Auth\MultiFactor\App;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Auth\MultiFactor\App\Actions\DisableAppAuthenticationAction;
use Filament\Auth\MultiFactor\App\Actions\RegenerateAppAuthenticationRecoveryCodesAction;
use Filament\Auth\MultiFactor\App\Actions\SetUpAppAuthenticationAction;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LogicException;
use PragmaRX\Google2FAQRCode\Google2FA;

class AppAuthentication implements MultiFactorAuthenticationProvider
{
    protected bool $isRecoverable = false;

    protected bool $canRegenerateRecoveryCodes = true;

    protected int $recoveryCodeCount = 8;

    protected ?string $brandName = null;

    /**
     * 8 keys (respectively 4 minutes) past and future
     */
    protected int $codeWindow = 8;

    public function __construct(
        protected Google2FA $google2FA,
    ) {}

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'app';
    }

    public function getLoginFormLabel(): string
    {
        return __('filament-panels::auth/multi-factor/app/provider.login_form.label');
    }

    public function isEnabled(Authenticatable $user): bool
    {
        if (! ($user instanceof HasAppAuthentication)) {
            throw new LogicException('The user model must implement the [' . HasAppAuthentication::class . '] interface to use app authentication.');
        }

        return filled($user->getAppAuthenticationSecret());
    }

    public function getHolderName(HasAppAuthentication $user): string
    {
        return $user->getAppAuthenticationHolderName();
    }

    public function getSecret(HasAppAuthentication $user): string
    {
        $secret = $user->getAppAuthenticationSecret();

        if (blank($secret)) {
            throw new LogicException('The user does not have a app authentication secret.');
        }

        return $secret;
    }

    public function saveSecret(HasAppAuthentication $user, ?string $secret): void
    {
        $user->saveAppAuthenticationSecret($secret);
    }

    /**
     * @return array<string>
     */
    public function getRecoveryCodes(HasAppAuthenticationRecovery $user): array
    {
        $codes = $user->getAppAuthenticationRecoveryCodes();

        if (blank($codes)) {
            throw new LogicException('The user does not have any app authentication recovery codes.');
        }

        return $codes;
    }

    /**
     * @param  array<string> | null  $codes
     */
    public function saveRecoveryCodes(HasAppAuthenticationRecovery $user, ?array $codes): void
    {
        if (! is_array($codes)) {
            $user->saveAppAuthenticationRecoveryCodes(null);

            return;
        }

        $user->saveAppAuthenticationRecoveryCodes(array_map(
            fn (string $code): string => Hash::make($code),
            $codes,
        ));
    }

    public function generateSecret(): string
    {
        return $this->google2FA->generateSecretKey(16);
    }

    public function getCurrentCode(HasAppAuthentication $user, ?string $secret = null): string
    {
        return $this->google2FA->getCurrentOtp($secret ?? $this->getSecret($user));
    }

    public function generateQrCodeDataUri(string $secret): string
    {
        /** @var HasAppAuthentication $user */
        $user = Filament::auth()->user();

        $inlineQrCode = $this->google2FA->getQRCodeInline(
            $this->getBrandName(),
            $this->getHolderName($user),
            $secret,
        );

        // This is a fallback for when `bacon/bacon-qr-code` is installed but the `imagick` extension is not.
        if (
            class_exists(Writer::class)
            && class_exists(ImageRenderer::class)
            && (! extension_loaded('imagick'))
        ) {
            $inlineQrCode = 'data:image/svg+xml;base64,' . base64_encode($inlineQrCode);
        }

        return $inlineQrCode;
    }

    /**
     * @return array<string>
     */
    public function generateRecoveryCodes(): array
    {
        return Collection::times($this->getRecoveryCodeCount(), fn (): string => Str::random(10) . '-' . Str::random(10))->all();
    }

    public function verifyCode(string $code, ?string $secret = null): bool
    {
        /** @var HasAppAuthentication $user */
        $user = Filament::auth()->user();

        return $this->google2FA->verifyKey($secret ?? $this->getSecret($user), $code, $this->getCodeWindow());
    }

    public function verifyRecoveryCode(string $recoveryCode, ?HasAppAuthenticationRecovery $user = null): bool
    {
        $user ??= Filament::auth()->user();

        $remainingCodes = [];
        $isValid = false;

        foreach ($this->getRecoveryCodes($user) as $hashedRecoveryCode) { /** @phpstan-ignore-line */
            if (Hash::check($recoveryCode, $hashedRecoveryCode)) {
                $isValid = true;

                continue;
            }

            $remainingCodes[] = $hashedRecoveryCode;
        }

        if ($isValid) {
            $user->saveAppAuthenticationRecoveryCodes($remainingCodes);
        }

        return $isValid;
    }

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public function getManagementSchemaComponents(): array
    {
        $user = Filament::auth()->user();

        return [
            Actions::make($this->getActions())
                ->label(__('filament-panels::auth/multi-factor/app/provider.management_schema.actions.label'))
                ->belowContent(__('filament-panels::auth/multi-factor/app/provider.management_schema.actions.below_content'))
                ->afterLabel(fn (): Text => $this->isEnabled($user)
                    ? Text::make(__('filament-panels::auth/multi-factor/app/provider.management_schema.actions.messages.enabled'))
                        ->badge()
                        ->color('success')
                    : Text::make(__('filament-panels::auth/multi-factor/app/provider.management_schema.actions.messages.disabled'))
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
            SetUpAppAuthenticationAction::make($this)
                ->hidden(fn (): bool => $this->isEnabled($user)),
            RegenerateAppAuthenticationRecoveryCodesAction::make($this)
                ->visible(fn (): bool => $this->isEnabled($user) && $this->isRecoverable() && $this->canRegenerateRecoveryCodes()),
            DisableAppAuthenticationAction::make($this)
                ->visible(fn (): bool => $this->isEnabled($user)),
        ];
    }

    public function recoverable(bool $condition = true): static
    {
        $this->isRecoverable = $condition;

        return $this;
    }

    public function isRecoverable(): bool
    {
        return $this->isRecoverable;
    }

    public function regenerableRecoveryCodes(bool $condition = true): static
    {
        $this->canRegenerateRecoveryCodes = $condition;

        return $this;
    }

    public function canRegenerateRecoveryCodes(): bool
    {
        return $this->canRegenerateRecoveryCodes;
    }

    public function brandName(?string $brandName): static
    {
        $this->brandName = $brandName;

        return $this;
    }

    public function getBrandName(): string
    {
        return $this->brandName ?? strip_tags(Filament::getBrandName());
    }

    public function recoveryCodeCount(int $count): static
    {
        $this->recoveryCodeCount = $count;

        return $this;
    }

    public function getRecoveryCodeCount(): int
    {
        return $this->recoveryCodeCount;
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

    /**
     * @param  Authenticatable&HasAppAuthentication&HasAppAuthenticationRecovery  $user
     */
    public function getChallengeFormComponents(Authenticatable $user): array
    {
        $isRecoverable = $this->isRecoverable();

        return [
            OneTimeCodeInput::make('code')
                ->label(__('filament-panels::auth/multi-factor/app/provider.login_form.code.label'))
                ->belowContent(fn (Get $get): Action => Action::make('useRecoveryCode')
                    ->label(__('filament-panels::auth/multi-factor/app/provider.login_form.code.actions.use_recovery_code.label'))
                    ->link()
                    ->action(fn (Set $set) => $set('useRecoveryCode', true))
                    ->visible(fn (): bool => $isRecoverable && (! $get('useRecoveryCode'))))
                ->validationAttribute(__('filament-panels::auth/multi-factor/app/provider.login_form.code.validation_attribute'))
                ->required(fn (Get $get): bool => (! $isRecoverable) || blank($get('recoveryCode')))
                ->rule(function () use ($user): Closure {
                    return function (string $attribute, $value, Closure $fail) use ($user): void {
                        if ($this->verifyCode($value, $this->getSecret($user))) {
                            return;
                        }

                        $fail(__('filament-panels::auth/multi-factor/app/provider.login_form.code.messages.invalid'));
                    };
                }),
            TextInput::make('recoveryCode')
                ->label(__('filament-panels::auth/multi-factor/app/provider.login_form.recovery_code.label'))
                ->validationAttribute(__('filament-panels::auth/multi-factor/app/provider.login_form.recovery_code.validation_attribute'))
                ->password()
                ->revealable(Filament::arePasswordsRevealable())
                ->rule(function () use ($user): Closure {
                    return function (string $attribute, mixed $value, Closure $fail) use ($user): void {
                        if (blank($value)) {
                            return;
                        }

                        if (is_string($value) && $this->verifyRecoveryCode($value, $user)) {
                            return;
                        }

                        $fail(__('filament-panels::auth/multi-factor/app/provider.login_form.recovery_code.messages.invalid'));
                    };
                })
                ->visible(fn (Get $get): bool => $isRecoverable && $get('useRecoveryCode'))
                ->live(onBlur: true),
        ];
    }
}
