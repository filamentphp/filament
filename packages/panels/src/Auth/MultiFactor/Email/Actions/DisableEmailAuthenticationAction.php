<?php

namespace Filament\Auth\MultiFactor\Email\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class DisableEmailAuthenticationAction
{
    public static function make(EmailAuthentication $emailAuthentication): Action
    {
        return Action::make('disableEmailAuthentication')
            ->label(__('filament-panels::auth/multi-factor/email/actions/disable.label'))
            ->color('danger')
            ->icon(Heroicon::LockOpen)
            ->link()
            ->mountUsing(function () use ($emailAuthentication): void {
                /** @var HasEmailAuthentication $user */
                $user = Filament::auth()->user();

                $emailAuthentication->sendCode($user);
            })
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::OutlinedLockOpen)
            ->modalHeading(__('filament-panels::auth/multi-factor/email/actions/disable.modal.heading'))
            ->modalDescription(__('filament-panels::auth/multi-factor/email/actions/disable.modal.description'))
            ->schema([
                OneTimeCodeInput::make('code')
                    ->label(__('filament-panels::auth/multi-factor/email/actions/disable.modal.form.code.label'))
                    ->validationAttribute(__('filament-panels::auth/multi-factor/email/actions/disable.modal.form.code.validation_attribute'))
                    ->belowContent(Action::make('resend')
                        ->label(__('filament-panels::auth/multi-factor/email/actions/disable.modal.form.code.actions.resend.label'))
                        ->link()
                        ->action(function () use ($emailAuthentication): void {
                            /** @var HasEmailAuthentication $user */
                            $user = Filament::auth()->user();

                            if (! $emailAuthentication->sendCode($user)) {
                                Notification::make()
                                    ->title(__('filament-panels::auth/multi-factor/email/actions/disable.modal.form.code.actions.resend.notifications.throttled.title'))
                                    ->danger()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title(__('filament-panels::auth/multi-factor/email/actions/disable.modal.form.code.actions.resend.notifications.resent.title'))
                                ->success()
                                ->send();
                        }))
                    ->required()
                    ->rule(function () use ($emailAuthentication): Closure {
                        return function (string $attribute, mixed $value, Closure $fail) use ($emailAuthentication): void {
                            if (is_string($value) && $emailAuthentication->verifyCode($value)) {
                                return;
                            }

                            $fail(__('filament-panels::auth/multi-factor/email/actions/disable.modal.form.code.messages.invalid'));
                        };
                    }),
            ])
            ->modalSubmitAction(fn (Action $action) => $action
                ->label(__('filament-panels::auth/multi-factor/email/actions/disable.modal.actions.submit.label')))
            ->action(function (): void {
                /** @var HasEmailAuthentication $user */
                $user = Filament::auth()->user();

                DB::transaction(function () use ($user): void {
                    $user->toggleEmailAuthentication(false);
                });

                Notification::make()
                    ->title(__('filament-panels::auth/multi-factor/email/actions/disable.notifications.disabled.title'))
                    ->success()
                    ->icon(Heroicon::OutlinedLockOpen)
                    ->send();
            })
            ->rateLimit(5);
    }
}
