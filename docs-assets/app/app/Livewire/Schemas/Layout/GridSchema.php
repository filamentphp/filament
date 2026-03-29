<?php

namespace App\Livewire\Schemas\Layout;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;

class GridSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('dense')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Fieldset::make('Dense')
                        ->columns(1)
                        ->dense()
                        ->schema([
                            TextEntry::make('name')
                                ->state('Dan Harrin'),
                            TextEntry::make('role')
                                ->state('Admin'),
                        ]),
                ]),
            Group::make()
                ->id('noGap')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Fieldset::make('No gap')
                        ->columns(1)
                        ->gap(false)
                        ->schema([
                            TextEntry::make('name')
                                ->state('Dan Harrin'),
                            TextEntry::make('role')
                                ->state('Admin'),
                        ]),
                ]),
            Group::make()
                ->id('grid')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Grid::make(3)
                        ->statePath('grid')
                        ->schema([
                            TextInput::make('first_name')
                                ->default('Dan'),
                            TextInput::make('last_name')
                                ->default('Harrin'),
                            TextInput::make('email')
                                ->default('dan@filamentphp.com')
                                ->columnSpanFull(),
                            Select::make('role')
                                ->default('admin')
                                ->options([
                                    'admin' => 'Admin',
                                    'editor' => 'Editor',
                                    'viewer' => 'Viewer',
                                ]),
                            Select::make('status')
                                ->default('active')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ]),
                            Select::make('department')
                                ->default('engineering')
                                ->options([
                                    'engineering' => 'Engineering',
                                    'marketing' => 'Marketing',
                                    'sales' => 'Sales',
                                ]),
                        ]),
                ]),
            Group::make()
                ->id('gridColumnSpan')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Grid::make(3)
                        ->statePath('gridColumnSpan')
                        ->schema([
                            TextInput::make('name')
                                ->default('Dan Harrin')
                                ->columnSpan(2),
                            TextInput::make('email')
                                ->default('dan@filament.test'),
                            Textarea::make('bio')
                                ->default('Full-stack developer and creator of Filament.')
                                ->columnSpanFull(),
                        ]),
                ]),
            Group::make()
                ->id('gridColumnStart')
                ->extraAttributes([
                    'class' => 'p-16 max-w-3xl',
                ])
                ->schema([
                    Grid::make(3)
                        ->statePath('gridColumnStart')
                        ->schema([
                            TextInput::make('name')
                                ->default('Dan Harrin'),
                            TextInput::make('email')
                                ->default('dan@filament.test')
                                ->columnStart(3),
                        ]),
                ]),
            Group::make()
                ->id('gridColumnOrder')
                ->extraAttributes([
                    'class' => 'p-16 max-w-3xl',
                ])
                ->schema([
                    Grid::make(3)
                        ->statePath('gridColumnOrder')
                        ->schema([
                            TextInput::make('first')
                                ->default('First in markup')
                                ->columnOrder(3),
                            TextInput::make('second')
                                ->default('Second in markup')
                                ->columnOrder(1),
                            TextInput::make('third')
                                ->default('Third in markup')
                                ->columnOrder(2),
                        ]),
                ]),
            Group::make()
                ->id('flex')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Flex::make([
                        Section::make([
                            TextInput::make('title')
                                ->default('Lorem ipsum dolor sit amet'),
                            Textarea::make('content')
                                ->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec euismod, nisl eget tempor aliquam, nunc nisl aliquet nunc, quis aliquam nisl nunc quis nisl. Donec euismod, nisl eget tempor aliquam, nunc nisl aliquet nunc, quis aliquam nisl nunc quis nisl.')
                                ->rows(5),
                        ]),
                        Section::make([
                            Toggle::make('is_published')
                                ->default(true),
                            Toggle::make('is_featured'),
                        ])->grow(false),
                    ])->statePath('flex'),
                ]),
        ];
    }
}
