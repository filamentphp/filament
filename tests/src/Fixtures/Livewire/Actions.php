<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Actions extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('textInput')
                    ->registerActions([
                        Action::make('setValue')
                            ->schema([
                                TextInput::make('value')
                                    ->default('foo')
                                    ->required(),
                            ])
                            ->action(function (TextInput $component, array $data): void {
                                $component->state($data['value']);
                            }),
                        Action::make('setValueFromArguments')
                            ->action(function (TextInput $component, array $arguments): void {
                                $component->state($arguments['value']);
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
            ])
            ->statePath('data');
    }

    public function data($data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function render(): View
    {
        return view('livewire.form-actions');
    }
}
