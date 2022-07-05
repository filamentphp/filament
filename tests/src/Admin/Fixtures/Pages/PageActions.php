<?php

namespace Filament\Tests\Admin\Fixtures\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;

class PageActions extends Page
{
    protected static string $view = 'admin.fixtures.pages.actions';

    protected function getActions(): array
    {
        return [
            Action::make('simple')
                ->action(function () {
                    $this->emit('simple-called');
                }),
            Action::make('form')
                ->form([
                    TextInput::make('payload')->required(),
                ])
                ->action(function (array $data) {
                    $this->emit('form-called', $data);
                }),
            Action::make('arguments')
                ->requiresConfirmation()
                ->action(function (array $arguments) {
                    $this->emit('arguments-called', $arguments);
                }),
            Action::make('hold')
                ->requiresConfirmation()
                ->action(function (Action $action) {
                    $this->emit('hold-called');

                    $action->hold();
                }),
        ];
    }
}
