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
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class SetUpEmailAuthenticationAction
{
    public static function make(EmailAuthentication $emailAuthentication): Action
    {
        return Action::make('setUpEmailAuthentication')
            ->label(__('filament-panels::auth/multi-factor/email/actions/set-up.label'))
            ->color('primary')
            ->icon(Heroicon::LockClosed)
            ->link()
            ->mountUsing(function () use ($emailAuthentication): void {
                /** @var HasEmailAuthentication $user */
                $user = Filament::auth()->user();

                $emailAuthentication->sendCode($user);
            })
            ->modalWidth(Width::Large)
            ->modalIcon(Heroicon::OutlinedLockClosed)
            ->modalIconColor('primary')
            ->modalHeading(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.heading'))
            ->modalDescription(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.description'))
            ->schema([
                OneTimeCodeInput::make('code')
                    ->label(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.label'))
                    ->belowContent(Action::make('resend')
                        ->label(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.actions.resend.label'))
                        ->link()
                        ->action(function () use ($emailAuthentication): void {
                            /** @var HasEmailAuthentication $user */
                            $user = Filament::auth()->user();

                            if (! $emailAuthentication->sendCode($user)) {
                                Notification::make()
                                    ->title(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.actions.resend.notifications.throttled.title'))
                                    ->danger()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.actions.resend.notifications.resent.title'))
                                ->success()
                                ->send();
                        }))
                    ->validationAttribute(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.validation_attribute'))
                    ->required()
                    ->rule(function () use ($emailAuthentication): Closure {
                        return function (string $attribute, $value, Closure $fail) use ($emailAuthentication): void {
                            if ($emailAuthentication->verifyCode($value)) {
                                return;
                            }

                            $fail(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.form.code.messages.invalid'));
                        };
                    }),
            ])
            ->modalSubmitAction(fn (Action $action) => $action
                ->label(__('filament-panels::auth/multi-factor/email/actions/set-up.modal.actions.submit.label')))
            ->action(function (): void {
                /** @var Authenticatable&HasEmailAuthentication $user */
                $user = Filament::auth()->user();

                DB::transaction(function () use ($user): void {
                    $user->toggleEmailAuthentication(true);
                });

                Notification::make()
                    ->title(__('filament-panels::auth/multi-factor/email/actions/set-up.notifications.enabled.title'))
                    ->success()
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->send();
            })
            ->rateLimit(5);
    }
}
