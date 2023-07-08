<?php

namespace App\Livewire\Forms;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class GettingStartedDemo extends Component implements HasForms
{
    use InteractsWithForms;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Group::make()
                    ->id('fields')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->statePath('fields')
                    ->schema([
                        TextInput::make('title'),
                        TextInput::make('slug'),
                        RichEditor::make('content'),
                    ]),
                Group::make()
                    ->id('columns')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->statePath('columns')
                    ->schema([
                        TextInput::make('title'),
                        TextInput::make('slug'),
                        RichEditor::make('content'),
                    ])
                    ->columns(2),
                Group::make()
                    ->id('columnSpan')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->statePath('columnSpan')
                    ->schema([
                        TextInput::make('title'),
                        TextInput::make('slug'),
                        RichEditor::make('content')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Group::make()
                    ->id('section')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->statePath('section')
                    ->schema([
                        TextInput::make('title'),
                        TextInput::make('slug'),
                        RichEditor::make('content')
                            ->columnSpan(2),
                        Section::make('Publishing')
                            ->description('Settings for publishing this post.')
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'reviewing' => 'Reviewing',
                                        'published' => 'Published',
                                    ]),
                                DateTimePicker::make('published_at'),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public function render()
    {
        return view('livewire.forms.getting-started');
    }
}
