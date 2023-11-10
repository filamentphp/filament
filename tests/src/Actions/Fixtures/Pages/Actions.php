<?php

namespace Filament\Tests\Actions\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Actions extends Page
{
    protected static string $view = 'actions.fixtures.pages.actions';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('simple')
                ->action(function () {
                    $this->dispatch('simple-called');
                }),
            Action::make('data')
                ->mountUsing(fn (ComponentContainer $form) => $form->fill(['foo' => 'bar']))
                ->form([
                    TextInput::make('payload')->required(),
                ])
                ->action(function (array $data) {
                    $this->dispatch('data-called', data: $data);
                }),
            Action::make('arguments')
                ->requiresConfirmation()
                ->action(function (array $arguments) {
                    $this->dispatch('arguments-called', arguments: $arguments);
                })
                ->extraModalFooterActions(fn (): array => [
                    Action::make('nested')
                        ->requiresConfirmation()
                        ->action(function (array $arguments) {
                            $this->dispatch('nested-called', arguments: $arguments);
                        }),
                ]),
            Action::make('halt')
                ->requiresConfirmation()
                ->action(function (Action $action) {
                    $this->dispatch('halt-called');

                    $action->halt();
                }),
            Action::make('visible'),
            Action::make('hidden')
                ->hidden(),
            Action::make('enabled'),
            Action::make('disabled')
                ->disabled(),
            Action::make('has-icon')
                ->icon('heroicon-m-pencil-square'),
            Action::make('has-label')
                ->label('My Action'),
            Action::make('has-color')
                ->color('primary'),
            Action::make('exists'),
            Action::make('url')
                ->url('https://filamentphp.com'),
            Action::make('url-in-new-tab')
                ->url('https://filamentphp.com', true),
            Action::make('url-not-in-new-tab')
                ->url('https://filamentphp.com', false),
            Action::make('shows-notification')
                ->action(function () {
                    Notification::make()
                        ->title('A notification')
                        ->success()
                        ->send();
                }),
            Action::make('does-not-show-notification'),
            Action::make('shows-notification-with-id')
                ->action(function () {
                    Notification::make('notification_with_id')
                        ->title('A notification')
                        ->success()
                        ->send();
                }),
            Action::make('two-notifications')
                ->action(function () {
                    Notification::make('first_notification')
                        ->title('First notification')
                        ->success()
                        ->send();
                    Notification::make('second_notification')
                        ->title('Second notification')
                        ->success()
                        ->send();
                }),
        ];
    }
}
