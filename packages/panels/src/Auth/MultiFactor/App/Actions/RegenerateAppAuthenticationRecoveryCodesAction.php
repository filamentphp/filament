<?php

namespace Filament\Auth\MultiFactor\App\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\UnorderedList;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;

class RegenerateAppAuthenticationRecoveryCodesAction
{
    public static function make(AppAuthentication $appAuthentication): Action
    {
        return Action::make('regenerateAppAuthenticationRecoveryCodes')
            ->label(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.label'))
            ->color('gray')
            ->icon(Heroicon::ArrowPath)
            ->link()
            ->modalWidth(Width::Large)
            ->modalIcon(Heroicon::OutlinedArrowPath)
            ->modalIconColor('primary')
            ->modalHeading(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.modal.heading'))
            ->modalDescription(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.modal.description'))
            ->schema([
                OneTimeCodeInput::make('code')
                    ->label(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.modal.form.code.label'))
                    ->validationAttribute(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.modal.form.code.validation_attribute'))
                    ->requiredWithout('password')
                    ->rule(function () use ($appAuthentication): Closure {
                        return function (string $attribute, $value, Closure $fail) use ($appAuthentication): void {
                            if ($appAuthentication->verifyCode($value)) {
                                return;
                            }

                            $fail(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.modal.form.code.messages.invalid'));
                        };
                    }),
                TextInput::make('password')
                    ->label(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.modal.form.password.label'))
                    ->validationAttribute(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.modal.form.password.validation_attribute'))
                    ->currentPassword(guard: Filament::getAuthGuard())
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->dehydrated(false),
            ])
            ->modalSubmitAction(fn (Action $action) => $action
                ->label(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.modal.actions.submit.label'))
                ->color('danger'))
            ->action(function (Action $action, HasActions $livewire) use ($appAuthentication): void {
                $recoveryCodes = $appAuthentication->generateRecoveryCodes();

                /** @var HasAppAuthenticationRecovery $user */
                $user = Filament::auth()->user();

                $appAuthentication->saveRecoveryCodes($user, $recoveryCodes);

                $livewire->mountAction('showNewRecoveryCodes', arguments: [
                    'recoveryCodes' => $recoveryCodes,
                ]);

                Notification::make()
                    ->title(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.notifications.regenerated.title'))
                    ->success()
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->send();
            })
            ->registerModalActions([
                Action::make('showNewRecoveryCodes')
                    ->modalHeading(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.show_new_recovery_codes.modal.heading'))
                    ->modalDescription(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.show_new_recovery_codes.modal.description'))
                    ->schema(fn (array $arguments) => [
                        Group::make([
                            UnorderedList::make(fn (): array => array_map(
                                fn (string $recoveryCode): Component => Text::make($recoveryCode)
                                    ->fontFamily(FontFamily::Mono)
                                    ->size('xs')
                                    ->color('neutral'),
                                $arguments['recoveryCodes'],
                            ))
                                ->size('xs'),
                            Text::make(fn (): Htmlable => new HtmlString(
                                __('filament-panels::auth/multi-factor/recovery-codes-modal-content.actions.0') .
                                ' ' .
                                Action::make('copy')
                                    ->label(__('filament-panels::auth/multi-factor/recovery-codes-modal-content.actions.copy.label'))
                                    ->link()
                                    ->alpineClickHandler('
                                        window.navigator.clipboard.writeText(' . Js::from(implode(PHP_EOL, $arguments['recoveryCodes'])) . ')
                                        $tooltip(' . Js::from(__('filament-panels::auth/multi-factor/recovery-codes-modal-content.messages.copied')) . ', {
                                            theme: $store.theme,
                                        })
                                    ')
                                    ->toHtml() .
                                ' ' .
                                __('filament-panels::auth/multi-factor/recovery-codes-modal-content.actions.1') .
                                ' ' .
                                Action::make('download')
                                    ->label(__('filament-panels::auth/multi-factor/recovery-codes-modal-content.actions.download.label'))
                                    ->link()
                                    ->url('data:application/octet-stream,' . urlencode(implode(PHP_EOL, $arguments['recoveryCodes'])))
                                    ->extraAttributes(['download' => true])
                                    ->toHtml() .
                                ' ' .
                                __('filament-panels::auth/multi-factor/recovery-codes-modal-content.actions.2')
                            )),
                        ])
                            ->dense(),
                    ])
                    ->modalWidth(Width::Large)
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false)
                    ->modalCloseButton(false)
                    ->modalSubmitAction(fn (Action $action) => $action
                        ->label(__('filament-panels::auth/multi-factor/app/actions/regenerate-recovery-codes.show_new_recovery_codes.modal.actions.submit.label'))
                        ->color('danger'))
                    ->modalCancelAction(false)
                    ->cancelParentActions(),
            ])
            ->rateLimit(5);
    }
}
