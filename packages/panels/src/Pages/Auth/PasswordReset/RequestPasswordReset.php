<?php

namespace Filament\Pages\Auth\PasswordReset;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Filament\Notifications\Notification;
use Filament\Pages\CardPage;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Password;

/**
 * @property Form $form
 */
class RequestPasswordReset extends CardPage
{
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected static string $view = 'filament::pages.auth.password-reset.request-password-reset';

    public ?string $email = '';

    public function mount(): void
    {
        if (Filament::auth()->check()) {
            redirect()->intended(Filament::getUrl());
        }

        $this->form->fill();
    }

    public function request(): void
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament::pages/auth/password-reset/request-password-reset.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->danger()
                ->send();

            return;
        }

        $data = $this->form->getState();

        $status = Password::sendResetLink(
            $data,
            function (CanResetPassword $user, string $token): void {
                if (! method_exists($user, 'notify')) {
                    $userClass = $user::class;

                    throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
                }

                $notification = new ResetPasswordNotification($token);
                $notification->url = Filament::getResetPasswordUrl($token, $user);

                $user->notify($notification);
            },
        );

        if ($status === Password::RESET_THROTTLED) {
            Notification::make()
                ->title(__($status))
                ->danger()
                ->send();

            return;
        }

        $this->form->fill();

        Notification::make()
            ->title(__(Password::RESET_LINK_SENT))
            ->success()
            ->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label(__('filament::pages/auth/password-reset/request-password-reset.fields.email.label'))
                    ->email()
                    ->required()
                    ->autocomplete()
                    ->autofocus(),
            ]);
    }

    public function requestAction(): Action
    {
        return Action::make('request')
            ->label(__('filament::pages/auth/password-reset/request-password-reset.buttons.request.label'))
            ->submit('request');
    }

    public function loginAction(): Action
    {
        return Action::make('login')
            ->link()
            ->label(__('filament::pages/auth/password-reset/request-password-reset.buttons.login.label'))
            ->icon(match (__('filament::layout.direction')) {
                'rtl' => 'heroicon-m-arrow-right',
                default => 'heroicon-m-arrow-left',
            })
            ->url(filament()->getLoginUrl());
    }

    public static function getName(): string
    {
        return 'filament.core.auth.password-reset.request-password-reset';
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament::pages/auth/password-reset/request-password-reset.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('filament::pages/auth/password-reset/request-password-reset.heading');
    }
}
