<?php

namespace App\Livewire\Schemas;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class OverviewDemo extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Group::make()
                    ->id('example')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Section::make('Details')
                                    ->schema([
                                        TextInput::make('name')
                                            ->default('Dan Harrin'),
                                        Select::make('position')
                                            ->options([
                                                'developer' => 'Developer',
                                                'designer' => 'Designer',
                                            ])
                                            ->default('developer'),
                                        Checkbox::make('is_admin')
                                            ->default(true),
                                    ]),
                                Section::make('Auditing')
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->dateTime()
                                            ->state('2024-09-24 14:30:32'),
                                        TextEntry::make('updated_at')
                                            ->dateTime()
                                            ->state('2024-10-15 09:12:57'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.schema.layout');
    }
}
