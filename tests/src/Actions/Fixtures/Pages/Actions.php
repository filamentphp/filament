<?php

namespace Filament\Tests\Actions\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;

class Actions extends Page
{
    protected static string $view = 'actions.fixtures.pages.actions';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('simple')
                ->action(function () {
                    $this->emit('simple-called');
                }),
            Action::make('data')
                ->mountUsing(fn (ComponentContainer $form) => $form->fill(['foo' => 'bar']))
                ->form([
                    TextInput::make('payload')->required(),
                ])
                ->action(function (array $data) {
                    $this->emit('data-called', $data);
                }),
            Action::make('arguments')
                ->requiresConfirmation()
                ->action(function (array $arguments) {
                    $this->emit('arguments-called', $arguments);
                }),
            Action::make('halt')
                ->requiresConfirmation()
                ->action(function (Action $action) {
                    $this->emit('halt-called');

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
            Action::make('url_in_new_tab')
                ->url('https://filamentphp.com', true),
            Action::make('url_not_in_new_tab')
                ->url('https://filamentphp.com', false),
        ];
    }
}
