<?php

namespace App\Livewire\Schemas\Layout;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class SectionSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('section')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Section::make('Rate limiting')
                        ->description('Prevent abuse by limiting the number of requests per period')
                        ->statePath('section')
                        ->schema([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->default('hour')
                                ->options([
                                    'hour' => 'Hour',
                                ]),
                            TextInput::make('maximum')
                                ->default(100),
                            Textarea::make('notes')
                                ->columnSpanFull(),
                        ])
                        ->columns(3),
                ]),
            Group::make()
                ->id('sectionHeaderActions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Section::make('Rate limiting')
                        ->description('Prevent abuse by limiting the number of requests per period')
                        ->afterHeader([
                            Action::make('test'),
                        ])
                        ->statePath('section')
                        ->schema([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->default('hour')
                                ->options([
                                    'hour' => 'Hour',
                                ]),
                            TextInput::make('maximum')
                                ->default(100),
                            Textarea::make('notes')
                                ->columnSpanFull(),
                        ])
                        ->columns(3),
                ]),
            Group::make()
                ->id('sectionFooterActions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Section::make('Rate limiting')
                        ->description('Prevent abuse by limiting the number of requests per period')
                        ->footer([
                            Action::make('test'),
                        ])
                        ->statePath('section')
                        ->schema([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->default('hour')
                                ->options([
                                    'hour' => 'Hour',
                                ]),
                            TextInput::make('maximum')
                                ->default(100),
                            Textarea::make('notes')
                                ->columnSpanFull(),
                        ])
                        ->columns(3),
                ]),
            Group::make()
                ->id('sectionIcons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Section::make('Cart')
                        ->description('The items you have selected for purchase')
                        ->icon(Heroicon::ShoppingBag)
                        ->statePath('sectionIcons')
                        ->schema([
                            Repeater::make('items')
                                ->hiddenLabel()
                                ->schema([
                                    Select::make('product')
                                        ->options([
                                            'tshirt' => 'Filament t-shirt',
                                        ]),
                                    TextInput::make('quantity'),
                                ])
                                ->columns(2)
                                ->reorderable(false)
                                ->addActionLabel('Add to order')
                                ->default([
                                    [
                                        'product' => 'tshirt',
                                        'quantity' => 3,
                                    ],
                                ]),
                            Textarea::make('specialOrderNotes'),
                        ]),
                ]),
            Group::make()
                ->id('sectionAside')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Section::make('Rate limiting')
                        ->description('Prevent abuse by limiting the number of requests per period')
                        ->aside()
                        ->statePath('sectionAside')
                        ->schema([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->default('hour')
                                ->options([
                                    'hour' => 'Hour',
                                ]),
                            TextInput::make('maximum')
                                ->default(100),
                            Textarea::make('notes'),
                        ]),
                ]),
            Group::make()
                ->id('sectionCollapsed')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Section::make('Cart')
                        ->description('The items you have selected for purchase')
                        ->collapsed()
                        ->statePath('sectionCollapsed'),
                ]),
            Group::make()
                ->id('sectionCompact')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Section::make('Rate limiting')
                        ->description('Prevent abuse by limiting the number of requests per period')
                        ->compact()
                        ->statePath('sectionCompact')
                        ->schema([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->default('hour')
                                ->options([
                                    'hour' => 'Hour',
                                ]),
                            TextInput::make('maximum')
                                ->default(100),
                            Textarea::make('notes')
                                ->columnSpanFull(),
                        ])
                        ->columns(3),
                ]),
            Group::make()
                ->id('sectionSecondary')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Section::make('Rate limiting')
                        ->description('Prevent abuse by limiting the number of requests per period')
                        ->statePath('sectionSecondary')
                        ->schema([
                            TextInput::make('hits')
                                ->default(30),
                            Select::make('period')
                                ->default('hour')
                                ->options([
                                    'hour' => 'Hour',
                                ]),
                            TextInput::make('maximum')
                                ->default(100),
                            Section::make('Notes')
                                ->compact()
                                ->secondary()
                                ->schema([
                                    Textarea::make('notes')
                                        ->hiddenLabel(),
                                ])
                                ->columnSpanFull(),
                        ])
                        ->columns(3),
                ]),
            Group::make()
                ->id('sectionColumns')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Section::make('User details')
                        ->description('Enter the user information below')
                        ->statePath('sectionColumns')
                        ->schema([
                            TextInput::make('first_name')
                                ->default('Dan'),
                            TextInput::make('last_name')
                                ->default('Harrin'),
                            TextInput::make('email')
                                ->default('dan@filamentphp.com'),
                            TextInput::make('phone')
                                ->default('+1 (555) 123-4567'),
                        ])
                        ->columns(2),
                ]),
            Group::make()
                ->id('sectionWithoutHeader')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Section::make([
                        TextInput::make('hits')
                            ->default(30),
                        Select::make('period')
                            ->default('hour')
                            ->options([
                                'hour' => 'Hour',
                            ]),
                        TextInput::make('maximum')
                            ->default(100),
                        Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                        ->statePath('sectionWithoutHeader')
                        ->columns(3),
                ]),
        ];
    }
}
