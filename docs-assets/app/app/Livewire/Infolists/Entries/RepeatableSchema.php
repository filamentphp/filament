<?php

namespace App\Livewire\Infolists\Entries;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;

class RepeatableSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('repeatable')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    RepeatableEntry::make('comments')
                        ->state([
                            ['author' => 'Dan Harrin', 'title' => 'Great framework!', 'content' => 'Filament has completely transformed how I build admin panels. The component system is incredibly intuitive.'],
                            ['author' => 'Ryan Chandler', 'title' => 'Love the flexibility', 'content' => 'Being able to customize every aspect while keeping things simple is what makes Filament stand out.'],
                        ])
                        ->schema([
                            TextEntry::make('author'),
                            TextEntry::make('title'),
                            TextEntry::make('content')
                                ->columnSpan(2),
                        ])
                        ->columns(2),
                ]),
            Group::make()
                ->id('repeatableGrid')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RepeatableEntry::make('comments')
                        ->state([
                            ['author' => 'Dan Harrin', 'title' => 'Great framework!', 'content' => 'Filament has completely transformed how I build admin panels. The component system is incredibly intuitive.'],
                            ['author' => 'Ryan Chandler', 'title' => 'Love the flexibility', 'content' => 'Being able to customize every aspect while keeping things simple is what makes Filament stand out.'],
                            ['author' => 'Zep Fietje', 'title' => 'Excellent docs', 'content' => 'The documentation is thorough and well-organized. Makes it easy to find what you need quickly.'],
                            ['author' => 'Dennis Koch', 'title' => 'Powerful tables', 'content' => 'The table builder alone saves hours of development time on every project.'],
                        ])
                        ->schema([
                            TextEntry::make('author'),
                            TextEntry::make('title'),
                            TextEntry::make('content')
                                ->columnSpan(2),
                        ])
                        ->columns(2)
                        ->grid(2),
                ]),
            Group::make()
                ->id('repeatableContainedFalse')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    RepeatableEntry::make('comments')
                        ->state([
                            ['author' => 'Dan Harrin', 'title' => 'Great framework!', 'content' => 'Filament has completely transformed how I build admin panels. The component system is incredibly intuitive.'],
                            ['author' => 'Ryan Chandler', 'title' => 'Love the flexibility', 'content' => 'Being able to customize every aspect while keeping things simple is what makes Filament stand out.'],
                        ])
                        ->schema([
                            TextEntry::make('author'),
                            TextEntry::make('title'),
                            TextEntry::make('content')
                                ->columnSpan(2),
                        ])
                        ->columns(2)
                        ->contained(false),
                ]),
            Group::make()
                ->id('repeatableTable')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    RepeatableEntry::make('comments')
                        ->state([
                            ['author' => 'Dan Harrin', 'title' => 'Great framework!', 'is_published' => true],
                            ['author' => 'Ryan Chandler', 'title' => 'Love the flexibility', 'is_published' => true],
                            ['author' => 'Zep Fietje', 'title' => 'Excellent docs', 'is_published' => false],
                        ])
                        ->table([
                            TableColumn::make('Author'),
                            TableColumn::make('Title'),
                            TableColumn::make('Published'),
                        ])
                        ->schema([
                            TextEntry::make('author'),
                            TextEntry::make('title'),
                            IconEntry::make('is_published')
                                ->boolean(),
                        ]),
                ]),
        ];
    }
}
