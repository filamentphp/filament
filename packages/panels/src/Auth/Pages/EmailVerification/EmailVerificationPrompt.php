<?php

namespace Filament\Auth\Pages\EmailVerification;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Auth\Notifications\VerifyEmail;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\SimplePage;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use LogicException;

/**
 * @property-read Action $resendNotificationAction
 */
class EmailVerificationPrompt extends SimplePage
{
    use WithRateLimiting;

    public function mount(): void
    {
        if ((! Filament::auth()->check()) || $this->getVerifiable()->hasVerifiedEmail()) {
            redirect()->intended(Filament::getUrl());
        }
    }

    protected function getVerifiable(): MustVerifyEmail
    {
        /** @var MustVerifyEmail $user */
        $user = Filament::auth()->user();

        return $user;
    }

    protected function sendEmailVerificationNotification(MustVerifyEmail $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (! method_exists($user, 'notify')) {
            $userClass = $user::class;

            throw new LogicException("Model [{$userClass}] does not have a [notify()] method.");
        }

        $notification = app(VerifyEmail::class);
        $notification->url = Filament::getVerifyEmailUrl($user);

        $user->notify($notification);
    }

    public function resendNotificationAction(): Action
    {
        return Action::make('resendNotification')
            ->link()
            ->label(__('filament-panels::auth/pages/email-verification/email-verification-prompt.actions.resend_notification.label') . '.')
            ->size('sm')
            ->action(function (): void {
                try {
                    $this->rateLimit(2);
                } catch (TooManyRequestsException $exception) {
                    $this->getRateLimitedNotification($exception)?->send();

                    return;
                }

                $this->sendEmailVerificationNotification($this->getVerifiable());

                Notification::make()
                    ->title(__('filament-panels::auth/pages/email-verification/email-verification-prompt.notifications.notification_resent.title'))
                    ->success()
                    ->send();
            });
    }

    protected function getRateLimitedNotification(TooManyRequestsException $exception): ?Notification
    {
        return Notification::make()
            ->title(__('filament-panels::auth/pages/email-verification/email-verification-prompt.notifications.notification_resend_throttled.title', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]))
            ->body(array_key_exists('body', __('filament-panels::auth/pages/email-verification/email-verification-prompt.notifications.notification_resend_throttled') ?: []) ? __('filament-panels::auth/pages/email-verification/email-verification-prompt.notifications.notification_resend_throttled.body', [
                'seconds' => $exception->secondsUntilAvailable,
                'minutes' => $exception->minutesUntilAvailable,
            ]) : null)
            ->danger();
    }

    public function getTitle(): string | Htmlable
    {
        return __('filament-panels::auth/pages/email-verification/email-verification-prompt.title');
    }

    public function getHeading(): string | Htmlable | null
    {
        return __('filament-panels::auth/pages/email-verification/email-verification-prompt.heading');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Text::make(__('filament-panels::auth/pages/email-verification/email-verification-prompt.messages.notification_sent', [
                    'email' => filament()->auth()->user()->getEmailForVerification(),
                ])),
                Text::make(new HtmlString(
                    __('filament-panels::auth/pages/email-verification/email-verification-prompt.messages.notification_not_received') .
                    ' ' .
                    $this->resendNotificationAction->toHtml(),
                )),
            ]);
    }
}
