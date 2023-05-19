<?php

namespace Filament\Pages\Auth\EmailVerification;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Notifications\Notification;
use Filament\Pages\CardPage;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property Form $form
 */
class EmailVerificationPrompt extends CardPage
{
    use WithRateLimiting;

    /**
     * @var view-string
     */
    protected static string $view = 'filament::pages.auth.email-verification.email-verification-prompt';

    public function mount(): void
    {
        /** @var MustVerifyEmail $user */
        $user = Filament::auth()->user();

        if ($user->hasVerifiedEmail()) {
            redirect()->intended(Filament::getUrl());
        }
    }

    public function resendNotificationAction(): Action
    {
        return Action::make('resendNotification')
            ->link()
            ->label(__('filament::pages/auth/email-verification/email-verification-prompt.buttons.resend_notification.label') . '.')
            ->action(function (): void {
                try {
                    $this->rateLimit(2);
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
            });
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
