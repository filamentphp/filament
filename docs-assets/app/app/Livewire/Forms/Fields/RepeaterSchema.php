<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Support\Enums\Alignment;

class RepeaterSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('repeater')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('repeater')
                        ->label('Members')
                        ->schema([
                            TextInput::make('name')->required(),
                            Select::make('role')
                                ->options([
                                    'member' => 'Member',
                                    'administrator' => 'Administrator',
                                    'owner' => 'Owner',
                                ])
                                ->required(),
                        ])
                        ->columns(2)
                        ->default([
                            [
                                'name' => 'Dan Harrin',
                                'role' => 'owner',
                            ],
                            [
                                'name' => 'Ryan Chandler',
                                'role' => 'administrator',
                            ],
                            [
                                'name' => 'Zep Fietje',
                                'role' => 'member',
                            ],
                            [
                                'name' => null,
                                'role' => null,
                            ],
                        ]),
                ]),
            Group::make()
                ->id('repeaterTable')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('repeaterTable')
                        ->label('Members')
                        ->table([
                            Repeater\TableColumn::make('Name'),
                            Repeater\TableColumn::make('Role'),
                        ])
                        ->schema([
                            TextInput::make('name')->required(),
                            Select::make('role')
                                ->options([
                                    'member' => 'Member',
                                    'administrator' => 'Administrator',
                                    'owner' => 'Owner',
                                ])
                                ->required(),
                        ])
                        ->columns(2)
                        ->default([
                            [
                                'name' => 'Dan Harrin',
                                'role' => 'owner',
                            ],
                            [
                                'name' => 'Ryan Chandler',
                                'role' => 'administrator',
                            ],
                            [
                                'name' => 'Zep Fietje',
                                'role' => 'member',
                            ],
                            [
                                'name' => null,
                                'role' => null,
                            ],
                        ]),
                ]),
            Group::make()
                ->id('repeaterTableCompact')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('repeaterTable')
                        ->label('Members')
                        ->table([
                            Repeater\TableColumn::make('Name'),
                            Repeater\TableColumn::make('Role'),
                        ])
                        ->compact()
                        ->schema([
                            TextInput::make('name')->required(),
                            Select::make('role')
                                ->options([
                                    'member' => 'Member',
                                    'administrator' => 'Administrator',
                                    'owner' => 'Owner',
                                ])
                                ->required(),
                        ])
                        ->columns(2)
                        ->default([
                            [
                                'name' => 'Dan Harrin',
                                'role' => 'owner',
                            ],
                            [
                                'name' => 'Ryan Chandler',
                                'role' => 'administrator',
                            ],
                            [
                                'name' => 'Zep Fietje',
                                'role' => 'member',
                            ],
                            [
                                'name' => null,
                                'role' => null,
                            ],
                        ]),
                ]),
            Group::make()
                ->id('repeaterReorderableWithButtons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('repeaterReorderableWithButtons')
                        ->label('Members')
                        ->schema([
                            TextInput::make('name')->required(),
                            Select::make('role')
                                ->options([
                                    'member' => 'Member',
                                    'administrator' => 'Administrator',
                                    'owner' => 'Owner',
                                ])
                                ->required(),
                        ])
                        ->columns(2)
                        ->default([
                            [
                                'name' => 'Dan Harrin',
                                'role' => 'owner',
                            ],
                            [
                                'name' => 'Ryan Chandler',
                                'role' => 'administrator',
                            ],
                            [
                                'name' => 'Zep Fietje',
                                'role' => 'member',
                            ],
                        ])
                        ->reorderableWithButtons(),
                ]),
            Group::make()
                ->id('collapsibleRepeater')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('collapsibleRepeater')
                        ->label('Qualifications')
                        ->schema([
                            TextInput::make('name')->required(),
                        ])
                        ->collapsible()
                        ->default([
                            ['name' => 'Tailwind CSS Level 1'],
                            ['name' => 'Alpine.js Level 1'],
                            ['name' => 'Laravel Level 1'],
                        ]),
                ]),
            Group::make()
                ->id('collapsedRepeater')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('collapsedRepeater')
                        ->label('Qualifications')
                        ->defaultItems(3)
                        ->collapsed(),
                ]),
            Group::make()
                ->id('cloneableRepeater')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('cloneableRepeater')
                        ->label('Qualifications')
                        ->schema([
                            TextInput::make('name')->required(),
                        ])
                        ->cloneable()
                        ->default([
                            ['name' => 'Tailwind CSS Level 1'],
                            ['name' => 'Alpine.js Level 1'],
                            ['name' => 'Laravel Level 1'],
                            ['name' => 'Livewire Level 1'],
                        ]),
                ]),
            Group::make()
                ->id('gridRepeater')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('gridRepeater')
                        ->label('Qualifications')
                        ->schema([
                            TextInput::make('name')->required(),
                        ])
                        ->grid(2)
                        ->default([
                            ['name' => 'Tailwind CSS Level 1'],
                            ['name' => 'Alpine.js Level 1'],
                            ['name' => 'Laravel Level 1'],
                            ['name' => 'Livewire Level 1'],
                        ]),
                ]),
            Group::make()
                ->id('labelledRepeater')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('labelledRepeater')
                        ->label('Members')
                        ->schema([
                            TextInput::make('name')->required(),
                            Select::make('role')
                                ->options([
                                    'member' => 'Member',
                                    'administrator' => 'Administrator',
                                    'owner' => 'Owner',
                                ])
                                ->required(),
                        ])
                        ->columns(2)
                        ->default([
                            [
                                'name' => 'Dan Harrin',
                                'role' => 'owner',
                            ],
                            [
                                'name' => 'Ryan Chandler',
                                'role' => 'administrator',
                            ],
                            [
                                'name' => 'Zep Fietje',
                                'role' => 'member',
                            ],
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                ]),
            Group::make()
                ->id('numberedRepeater')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('numberedRepeater')
                        ->label('Members')
                        ->schema([
                            TextInput::make('name')->required(),
                            Select::make('role')
                                ->options([
                                    'member' => 'Member',
                                    'administrator' => 'Administrator',
                                    'owner' => 'Owner',
                                ])
                                ->required(),
                        ])
                        ->columns(2)
                        ->default([
                            [
                                'name' => 'Dan Harrin',
                                'role' => 'owner',
                            ],
                            [
                                'name' => 'Ryan Chandler',
                                'role' => 'administrator',
                            ],
                            [
                                'name' => 'Zep Fietje',
                                'role' => 'member',
                            ],
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                        ->itemNumbers(),
                ]),
            Group::make()
                ->id('simpleRepeater')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('simpleRepeater')
                        ->label('Invitations')
                        ->simple(
                            TextInput::make('email')
                                ->email()
                                ->required(),
                        )
                        ->default([
                            'dan@filamentphp.com',
                            'ryan@filamentphp.com',
                        ]),
                ]),
            Group::make()
                ->id('repeaterAddActionAlignment')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    Repeater::make('repeaterAddActionAlignment')
                        ->label('Members')
                        ->schema([
                            TextInput::make('name')->required(),
                            Select::make('role')
                                ->options([
                                    'member' => 'Member',
                                    'administrator' => 'Administrator',
                                    'owner' => 'Owner',
                                ])
                                ->required(),
                        ])
                        ->columns(2)
                        ->addActionAlignment(Alignment::Start)
                        ->default([
                            [
                                'name' => 'Dan Harrin',
                                'role' => 'owner',
                            ],
                        ]),
                ]),
        ];
    }
}
