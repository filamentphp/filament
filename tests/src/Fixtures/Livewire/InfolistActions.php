<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class InfolistActions extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->constantState([])
            ->components([
                TextEntry::make('textEntry')
                    ->registerActions([
                        Action::make('setValue')
                            ->schema([
                                TextInput::make('value')
                                    ->default('foo')
                                    ->required(),
                            ])
                            ->action(function (array $data): void {
                                $this->dispatch('foo', $data['value']);
                            }),
                        Action::make('setValueFromArguments')
                            ->requiresConfirmation()
                            ->action(function (array $arguments): void {
                                $this->dispatch('foo', $arguments['value']);
                            }),
                        Action::make('halt')
                            ->requiresConfirmation()
                            ->action(function (Action $action): void {
                                $action->halt();
                            }),
                        Action::make('hidden')
                            ->hidden(),
                        Action::make('visible'),
                        Action::make('disabled')
                            ->disabled(),
                        Action::make('enabled'),
                        Action::make('hasIcon')
                            ->icon(Heroicon::PencilSquare),
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
        return view('livewire.infolist-actions');
    }
}
