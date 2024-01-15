<?php

namespace Filament\Tests\Forms\Fixtures;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Actions extends Component implements HasForms
{
    use InteractsWithForms;

    public $data;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('textInput')
                    ->registerActions([
                        Action::make('setValue')
                            ->form([
                                TextInput::make('value')
                                    ->default('foo')
                                    ->required(),
                            ])
                            ->action(function (TextInput $component, array $data) {
                                $component->state($data['value']);
                            }),
                        Action::make('setValueFromArguments')
                            ->action(function (TextInput $component, array $arguments) {
                                $component->state($arguments['value']);
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
        return view('forms.fixtures.actions');
    }
}
