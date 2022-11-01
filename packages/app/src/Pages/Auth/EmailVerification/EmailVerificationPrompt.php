<?php

namespace Filament\Pages\Auth\EmailVerification;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Pages\CardPage;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

/**
 * @property Form $form
 */
class EmailVerificationPrompt extends CardPage
{
    use WithRateLimiting;

    protected static string $view = 'filament::pages.auth.email-verification.email-verification-prompt';

    public function mount(): void
    {
        /** @var MustVerifyEmail $user */
        $user = Filament::auth()->user();

        if ($user->hasVerifiedEmail()) {
            redirect()->intended(Filament::getUrl());
        }
    }

    public function resendNotification(): void
    {
        try {
            $this->rateLimit(1);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament::pages/auth/email-verification/email-verification-prompt.messages.notification_resend_throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->danger()
                ->send();

            return;
        }

        $user = Filament::auth()->user();

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new Exception("Model [{$userClass}] does not have a [notify()] method.");
        }

        $notification = new VerifyEmail();
        $notification->url = Filament::getVerifyEmailUrl($user);

        $user->notify($notification);

        Notification::make()
            ->title(__('filament::pages/auth/email-verification/email-verification-prompt.messages.notification_resent'))
            ->success()
            ->send();
    }

    public static function getName(): string
    {
        return 'filament.core.auth.email-verification.email-verification-prompt';
    }

    public function getTitle(): string
    {
        return __('filament::pages/auth/email-verification/email-verification-prompt.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('filament::pages/auth/email-verification/email-verification-prompt.heading');
    }
}
