<?php

namespace Filament\Auth\MultiFactor\EmailCode\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\EmailCode\Contracts\HasEmailCodeAuthentication;
use Filament\Auth\MultiFactor\EmailCode\EmailCodeAuthentication;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class DisableEmailCodeAuthenticationAction
{
    public static function make(EmailCodeAuthentication $emailCodeAuthentication): Action
    {
        return Action::make('disableEmailCodeAuthentication')
            ->label(__('filament-panels::auth/multi-factor/email-code/actions/disable.label'))
            ->color('danger')
            ->icon(Heroicon::LockOpen)
            ->link()
            ->mountUsing(function () use ($emailCodeAuthentication): void {
                /** @var HasEmailCodeAuthentication $user */
                $user = Filament::auth()->user();

                $emailCodeAuthentication->sendCode($user);
            })
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedLockOpen)
            ->modalHeading(__('filament-panels::auth/multi-factor/email-code/actions/disable.modal.heading'))
            ->modalDescription(__('filament-panels::auth/multi-factor/email-code/actions/disable.modal.description'))
            ->schema([
                OneTimeCodeInput::make('code')
                    ->label(__('filament-panels::auth/multi-factor/email-code/actions/disable.modal.form.code.label'))
                    ->validationAttribute(__('filament-panels::auth/multi-factor/email-code/actions/disable.modal.form.code.validation_attribute'))
                    ->belowContent(Action::make('resend')
                        ->label(__('filament-panels::auth/multi-factor/email-code/actions/disable.modal.form.code.actions.resend.label'))
                        ->link()
                        ->action(function () use ($emailCodeAuthentication): void {
                            /** @var HasEmailCodeAuthentication $user */
                            $user = Filament::auth()->user();

                            $emailCodeAuthentication->sendCode($user);

                            Notification::make()
                                ->title(__('filament-panels::auth/multi-factor/email-code/actions/disable.modal.form.code.actions.resend.notifications.resent.title'))
                                ->success()
                                ->send();
                        }))
                    ->required()
                    ->rule(function () use ($emailCodeAuthentication): Closure {
                        return function (string $attribute, mixed $value, Closure $fail) use ($emailCodeAuthentication): void {
                            if (is_string($value) && $emailCodeAuthentication->verifyCode($value)) {
                                return;
                            }

                            $fail(__('filament-panels::auth/multi-factor/email-code/actions/disable.modal.form.code.messages.invalid'));
                        };
                    }),
            ])
            ->modalSubmitAction(fn (Action $action) => $action
                ->label(__('filament-panels::auth/multi-factor/email-code/actions/disable.modal.actions.submit.label')))
            ->action(function () use ($emailCodeAuthentication): void {
                /** @var HasEmailCodeAuthentication $user */
                $user = Filament::auth()->user();

                DB::transaction(function () use ($emailCodeAuthentication, $user): void {
                    $emailCodeAuthentication->saveSecret($user, null);
                });

                Notification::make()
                    ->title(__('filament-panels::auth/multi-factor/email-code/actions/disable.notifications.disabled.title'))
                    ->success()
                    ->icon(Heroicon::OutlinedLockOpen)
                    ->send();
            })
            ->rateLimit(5);
    }
}
