<?php

namespace Filament\Auth\MultiFactor\EmailCode\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Auth\MultiFactor\EmailCode\Contracts\HasEmailCodeAuthentication;
use Filament\Auth\MultiFactor\EmailCode\EmailCodeAuthentication;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class SetUpEmailCodeAuthenticationAction
{
    public static function make(EmailCodeAuthentication $emailCodeAuthentication): Action
    {
        return Action::make('setUpEmailCodeAuthentication')
            ->label(__('filament-panels::auth/multi-factor/email-code/actions/set-up.label'))
            ->color('primary')
            ->icon(Heroicon::LockClosed)
            ->link()
            ->mountUsing(function (HasActions $livewire) use ($emailCodeAuthentication): void {
                $livewire->mergeMountedActionArguments([
                    'encrypted' => encrypt([
                        'secret' => $secret = $emailCodeAuthentication->generateSecret(),
                        'userId' => Filament::auth()->id(),
                    ]),
                ]);

                /** @var HasEmailCodeAuthentication $user */
                $user = Filament::auth()->user();

                $emailCodeAuthentication->sendCode($user, $secret);
            })
            ->modalWidth(Width::Large)
            ->modalIcon(Heroicon::OutlinedLockClosed)
            ->modalIconColor('primary')
            ->modalHeading(__('filament-panels::auth/multi-factor/email-code/actions/set-up.modal.heading'))
            ->modalDescription(__('filament-panels::auth/multi-factor/email-code/actions/set-up.modal.description'))
            ->schema(fn (array $arguments): array => [
                OneTimeCodeInput::make('code')
                    ->label(__('filament-panels::auth/multi-factor/email-code/actions/set-up.modal.form.code.label'))
                    ->belowContent(Action::make('resend')
                        ->label(__('filament-panels::auth/multi-factor/email-code/actions/set-up.modal.form.code.actions.resend.label'))
                        ->link()
                        ->action(function () use ($arguments, $emailCodeAuthentication): void {
                            /** @var HasEmailCodeAuthentication $user */
                            $user = Filament::auth()->user();

                            $emailCodeAuthentication->sendCode($user, decrypt($arguments['encrypted'])['secret']);

                            Notification::make()
                                ->title(__('filament-panels::auth/multi-factor/email-code/actions/set-up.modal.form.code.actions.resend.notifications.resent.title'))
                                ->success()
                                ->send();
                        }))
                    ->validationAttribute(__('filament-panels::auth/multi-factor/email-code/actions/set-up.modal.form.code.validation_attribute'))
                    ->required()
                    ->rule(function () use ($arguments, $emailCodeAuthentication): Closure {
                        return function (string $attribute, $value, Closure $fail) use ($arguments, $emailCodeAuthentication): void {
                            if ($emailCodeAuthentication->verifyCode($value, decrypt($arguments['encrypted'])['secret'])) {
                                return;
                            }

                            $fail(__('filament-panels::auth/multi-factor/email-code/actions/set-up.modal.form.code.messages.invalid'));
                        };
                    }),
            ])
            ->modalSubmitAction(fn (Action $action) => $action
                ->label(__('filament-panels::auth/multi-factor/email-code/actions/set-up.modal.actions.submit.label')))
            ->action(function (array $arguments) use ($emailCodeAuthentication): void {
                /** @var Authenticatable&HasEmailCodeAuthentication $user */
                $user = Filament::auth()->user();

                $encrypted = decrypt($arguments['encrypted']);

                if ($user->getAuthIdentifier() !== $encrypted['userId']) {
                    // Avoid encrypted arguments being passed between users by verifying that the authenticated
                    // user is the same as the user that the encrypted arguments were issued for.
                    return;
                }

                DB::transaction(function () use ($emailCodeAuthentication, $encrypted, $user): void {
                    $emailCodeAuthentication->saveSecret($user, $encrypted['secret']);
                });

                Notification::make()
                    ->title(__('filament-panels::auth/multi-factor/email-code/actions/set-up.notifications.enabled.title'))
                    ->success()
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->send();
            })
            ->rateLimit(5);
    }
}
