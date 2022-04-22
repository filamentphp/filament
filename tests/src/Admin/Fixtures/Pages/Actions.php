<?php

namespace Filament\Tests\Admin\Fixtures\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\ButtonAction;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class Actions extends Page
{
    protected static string $view = 'fixtures.pages.actions';

    protected function getActions(): array|View|null
    {
        return [
            ButtonAction::make('foo')
                ->action(fn () => $this->dispatchBrowserEvent('foo-called')),
            ButtonAction::make('foo-form')
                ->form([
                    TextInput::make('name')
                        ->required()
                ])
                ->action(fn (array $data) => $this->dispatchBrowserEvent('foo-form-called', $data)),
        ];
    }
}
