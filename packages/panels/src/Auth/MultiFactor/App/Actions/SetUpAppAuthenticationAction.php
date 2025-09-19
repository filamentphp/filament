<?php

namespace Filament\Auth\MultiFactor\App\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Facades\Filament;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\UnorderedList;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;

class SetUpAppAuthenticationAction
{
    public static function make(AppAuthentication $appAuthentication): Action
    {
        return Action::make('setUpAppAuthentication')
            ->label(__('filament-panels::auth/multi-factor/app/actions/set-up.label'))
            ->color('primary')
            ->icon(Heroicon::LockClosed)
            ->link()
            ->mountUsing(function (HasActions $livewire) use ($appAuthentication): void {
                $livewire->mergeMountedActionArguments([
                    'encrypted' => encrypt([
                        'secret' => $appAuthentication->generateSecret(),
                        ...($appAuthentication->isRecoverable()
                            ? ['recoveryCodes' => $appAuthentication->generateRecoveryCodes()]
                            : []),
                        'userId' => Filament::auth()->id(),
                    ]),
                ]);
            })
            ->modalWidth(Width::Large)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalIcon(Heroicon::OutlinedLockClosed)
            ->modalIconColor('primary')
            ->modalHeading(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.heading'))
            ->modalDescription(new HtmlString(Blade::render(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.description'))))
            ->modifyWizardUsing(fn (Wizard $wizard) => $wizard->hiddenHeader())
            ->steps(fn (Action $action): array => [
                Step::make('app')
                    ->schema([
                        Group::make([
                            Text::make(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.content.qr_code.instruction'))
                                ->color('neutral'),
                            Image::make(
                                url: fn (): string => $appAuthentication->generateQrCodeDataUri(decrypt($action->getArguments()['encrypted'])['secret']),
                                alt: __('filament-panels::auth/multi-factor/app/actions/set-up.modal.content.qr_code.alt'),
                            )
                                ->imageHeight('12rem')
                                ->alignCenter(),
                            Flex::make([
                                Text::make(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.content.text_code.instruction'))
                                    ->color('neutral')
                                    ->grow(false),
                                Text::make(fn (): string => decrypt($action->getArguments()['encrypted'])['secret'])
                                    ->fontFamily(FontFamily::Mono)
                                    ->color('neutral')
                                    ->copyable()
                                    ->copyMessage(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.content.text_code.messages.copied'))
                                    ->grow(false),
                            ])->from('sm'),
                        ])
                            ->dense(),
                        OneTimeCodeInput::make('code')
                            ->label(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.form.code.label'))
                            ->belowContent(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.form.code.below_content'))
                            ->validationAttribute(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.form.code.validation_attribute'))
                            ->required()
                            ->rule(function () use ($action, $appAuthentication): Closure {
                                return function (string $attribute, $value, Closure $fail) use ($action, $appAuthentication): void {
                                    if ($appAuthentication->verifyCode($value, decrypt($action->getArguments()['encrypted'])['secret'])) {
                                        return;
                                    }

                                    $fail(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.form.code.messages.invalid'));
                                };
                            }),
                    ]),
                Step::make('recovery')
                    ->schema([
                        Text::make(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.content.recovery_codes.instruction'))
                            ->weight(FontWeight::Bold)
                            ->color('neutral'),
                        UnorderedList::make(fn (): array => array_map(
                            fn (string $recoveryCode): Component => Text::make($recoveryCode)
                                ->fontFamily(FontFamily::Mono)
                                ->size(TextSize::ExtraSmall)
                                ->color('neutral'),
                            decrypt($action->getArguments()['encrypted'] ?? encrypt([]))['recoveryCodes'] ?? [],
                        ))
                            ->size(TextSize::ExtraSmall),
                        Text::make(function () use ($action): Htmlable {
                            $recoveryCodes = decrypt($action->getArguments()['encrypted'])['recoveryCodes'];

                            return new HtmlString(
                                __('filament-panels::auth/multi-factor/recovery-codes-modal-content.actions.0') .
                                ' ' .
                                Action::make('copy')
                                    ->label(__('filament-panels::auth/multi-factor/recovery-codes-modal-content.actions.copy.label'))
                                    ->link()
                                    ->alpineClickHandler('
                                                    window.navigator.clipboard.writeText(' . Js::from(implode(PHP_EOL, $recoveryCodes)) . ')
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
                                    ->url('data:application/octet-stream,' . urlencode(implode(PHP_EOL, $recoveryCodes)))
                                    ->extraAttributes(['download' => true])
                                    ->toHtml() .
                                ' ' .
                                __('filament-panels::auth/multi-factor/recovery-codes-modal-content.actions.2')
                            );
                        }),
                    ])
                    ->visible($appAuthentication->isRecoverable()),
            ])
            ->modalSubmitAction(fn (Action $action) => $action
                ->label(__('filament-panels::auth/multi-factor/app/actions/set-up.modal.actions.submit.label')))
            ->action(function (array $arguments) use ($appAuthentication): void {
                /** @var Authenticatable&HasAppAuthentication&HasAppAuthenticationRecovery $user */
                $user = Filament::auth()->user();

                $encrypted = decrypt($arguments['encrypted']);

                if ($user->getAuthIdentifier() !== $encrypted['userId']) {
                    // Avoid encrypted arguments being passed between users by verifying that the authenticated
                    // user is the same as the user that the encrypted arguments were issued for.
                    return;
                }

                DB::transaction(function () use ($appAuthentication, $encrypted, $user): void {
                    $appAuthentication->saveSecret($user, $encrypted['secret']);

                    if ($appAuthentication->isRecoverable()) {
                        $appAuthentication->saveRecoveryCodes($user, $encrypted['recoveryCodes']);
                    }
                });

                Notification::make()
                    ->title(__('filament-panels::auth/multi-factor/app/actions/set-up.notifications.enabled.title'))
                    ->success()
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->send();
            })
            ->rateLimit(5);
    }
}
