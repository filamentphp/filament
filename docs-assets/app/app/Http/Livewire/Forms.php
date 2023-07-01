<?php

namespace App\Http\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class Forms extends Component implements HasForms
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'simple',
                    ])
                    ->schema([
                        TextInput::make('simple')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'helperText',
                    ])
                    ->schema([
                        TextInput::make('helperText')
                            ->label('Name')
                            ->default('Dan Harrin')
                            ->helperText('Your **full name** here, including any middle names.'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'hint',
                    ])
                    ->schema([
                        TextInput::make('hint')
                            ->label('Password')
                            ->password()
                            ->default('password')
                            ->hint('[Forgotten your password?](forgotten-password)'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'hintColor',
                    ])
                    ->schema([
                        RichEditor::make('hintColor')
                            ->label('Content')
                            ->default('Filament es el mejor.')
                            ->hint('Translatable')
                            ->hintColor('primary'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'hintIcon',
                    ])
                    ->schema([
                        RichEditor::make('hintIcon')
                            ->label('Content')
                            ->default('Filament es el mejor.')
                            ->hint('Translatable')
                            ->hintIcon('heroicon-m-language'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'disabled',
                    ])
                    ->schema([
                        TextInput::make('disabled')
                            ->label('Name')
                            ->disabled()
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'placeholder',
                    ])
                    ->schema([
                        TextInput::make('placeholder')
                            ->label('Name')
                            ->placeholder('John Doe'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'textInput',
                    ])
                    ->schema([
                        TextInput::make('textInput')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'textInputAffix',
                    ])
                    ->schema([
                        TextInput::make('textInputAffix')
                            ->label('Domain')
                            ->default('filamentphp')
                            ->prefix('https://')
                            ->suffix('.com'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'textInputSuffixIcon',
                    ])
                    ->schema([
                        TextInput::make('textInputSuffixIcon')
                            ->label('Domain')
                            ->default('https://filamentphp.com')
                            ->suffixIcon('heroicon-m-globe-alt'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'select',
                    ])
                    ->schema([
                        Select::make('select')
                            ->label('Status'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-72 max-w-xl',
                        'id' => 'searchableSelect',
                    ])
                    ->schema([
                        Select::make('searchableSelect')
                            ->label('Author')
                            ->searchable()
                            ->options([
                                'dan' => 'Dan Harrin',
                                'ryan' => 'Ryan Chandler',
                                'zep' => 'Zep Fietje',
                                'adam' => 'Adam Weston',
                                'dennis' => 'Dennis Koch',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-44 max-w-xl',
                        'id' => 'multipleSelect',
                    ])
                    ->schema([
                        Select::make('multipleSelect')
                            ->label('Technologies')
                            ->multiple()
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'createSelectOption',
                    ])
                    ->schema([
                        Select::make('createSelectOption')
                            ->label('Author')
                            ->createOptionForm([
                                TextInput::make('name'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'editSelectOption',
                    ])
                    ->schema([
                        Select::make('editSelectOption')
                            ->label('Author')
                            ->default('dan')
                            ->options([
                                'dan' => 'Dan Harrin',
                            ])
                            ->fillEditOptionActionFormUsing(fn () => ['name' => 'Dan Harrin'])
                            ->editOptionForm([
                                TextInput::make('name'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'selectAffix',
                    ])
                    ->schema([
                        Select::make('selectAffix')
                            ->label('Domain')
                            ->default('filament')
                            ->options([
                                'filament' => 'filamentphp'
                            ])
                            ->prefix('https://')
                            ->suffix('.com'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'selectSuffixIcon',
                    ])
                    ->schema([
                        Select::make('selectSuffixIcon')
                            ->label('Domain')
                            ->default('filament')
                            ->options([
                                'filament' => 'filamentphp'
                            ])
                            ->suffixIcon('heroicon-m-globe-alt'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'checkbox',
                    ])
                    ->schema([
                        Checkbox::make('checkbox')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'inlineCheckbox',
                    ])
                    ->schema([
                        Checkbox::make('inlineCheckbox')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'notInlineCheckbox',
                    ])
                    ->schema([
                        Checkbox::make('notInlineCheckbox')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'toggle',
                    ])
                    ->schema([
                        Toggle::make('toggle')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'toggleIcons',
                    ])
                    ->schema([
                        Toggle::make('toggleIcons')
                            ->label('Is admin')
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-user'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'toggleOffColor',
                    ])
                    ->schema([
                        Toggle::make('toggleOffColor')
                            ->label('Is admin')
                            ->default(false)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'toggleOnColor',
                    ])
                    ->schema([
                        Toggle::make('toggleOnColor')
                            ->label('Is admin')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'inlineToggle',
                    ])
                    ->schema([
                        Toggle::make('inlineToggle')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'notInlineToggle',
                    ])
                    ->schema([
                        Toggle::make('notInlineToggle')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.forms');
    }
}
