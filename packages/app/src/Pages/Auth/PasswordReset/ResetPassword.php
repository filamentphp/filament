<?php

namespace Filament\Pages\Auth\PasswordReset;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse;
use Filament\Notifications\Notification;
use Filament\Pages\CardPage;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * @property Form $form
 */
class ResetPassword extends CardPage
{
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected static string $view = 'filament::pages.auth.password-reset.reset-password';

    public ?string $email = null;

    public ?string $password = '';

    public ?string $passwordConfirmation = '';

    public ?string $token = null;

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->token = request()->query('token');

        $this->form->fill([
            'email' => request()->query('email'),
        ]);
    }

    public function resetPassword(): ?PasswordResetResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament::pages/auth/password-reset/reset-password.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        $data['email'] = $this->email;
        $data['token'] = $this->token;

        $status = Password::reset(
            $data,
            function (CanResetPassword | Model | Authenticatable $user) use ($data) {
                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            Notification::make()
                ->title(__($status))
                ->success()
                ->send();

            return app(PasswordResetResponse::class);
        }

        Notification::make()
            ->title(__($status))
            ->danger()
            ->send();

        return null;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label(__('filament::pages/auth/password-reset/reset-password.fields.email.label'))
                    ->disabled()
                    ->autofocus(),
                TextInput::make('password')
                    ->label(__('filament::pages/auth/password-reset/reset-password.fields.password.label'))
                    ->password()
                    ->required()
                    ->rule(PasswordRule::default())
                    ->same('passwordConfirmation')
                    ->validationAttribute(__('filament::pages/auth/password-reset/reset-password.fields.password.validation_attribute')),
                TextInput::make('passwordConfirmation')
                    ->label(__('filament::pages/auth/password-reset/reset-password.fields.passwordConfirmation.label'))
                    ->password()
                    ->required()
                    ->dehydrated(false),
            ]);
    }

    public function resetPasswordAction(): Action
    {
        return Action::make('resetPasswordAction')
            ->label(__('filament::pages/auth/password-reset/reset-password.buttons.reset.label'))
            ->submit('resetPassword');
    }

    /**
     * @param  string  $propertyName
     */
    public function propertyIsPublicAndNotDefinedOnBaseClass($propertyName): bool
    {
        if ((! app()->runningUnitTests()) && in_array($propertyName, [
            'email',
            'token',
        ])) {
            return false;
        }

        return parent::propertyIsPublicAndNotDefinedOnBaseClass($propertyName);
    }

    public static function getName(): string
    {
        return 'filament.core.auth.password-reset.reset-password';
    }

    public function getTitle(): string
    {
        return __('filament::pages/auth/password-reset/reset-password.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('filament::pages/auth/password-reset/reset-password.heading');
    }
}
