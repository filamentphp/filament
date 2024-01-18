<?php

namespace Filament\Tests\Infolists\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Actions extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state([])
            ->schema([
                TextEntry::make('textEntry')
                    ->registerActions([
                        Action::make('setValue')
                            ->form([
                                TextInput::make('value')
                                    ->default('foo')
                                    ->required(),
                            ])
                            ->action(function (array $data) {
                                $this->dispatch('foo', $data['value']);
                            }),
                        Action::make('setValueFromArguments')
                            ->requiresConfirmation()
                            ->action(function (array $arguments) {
                                $this->dispatch('foo', $arguments['value']);
                            }),
                        Action::make('halt')
                            ->requiresConfirmation()
                            ->action(function (Action $action) {
                                $action->halt();
                            }),
                        Action::make('hidden')
                            ->hidden(),
                        Action::make('visible'),
                        Action::make('disabled')
                            ->disabled(),
                        Action::make('enabled'),
                        Action::make('hasIcon')
                            ->icon('heroicon-m-pencil-square'),
                        Action::make('hasLabel')
                            ->label('My Action'),
                        Action::make('hasColor')
                            ->color('primary'),
                        Action::make('url')
                            ->url('https://filamentphp.com'),
                        Action::make('urlInNewTab')
                            ->url('https://filamentphp.com', true),
                        Action::make('urlNotInNewTab')
                            ->url('https://filamentphp.com'),
                        Action::make('exists'),
                    ]),
            ]);
    }

    public function render(): View
    {
        return view('infolists.fixtures.actions');
    }
}
