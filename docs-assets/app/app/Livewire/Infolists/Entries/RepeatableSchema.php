<?php

namespace App\Livewire\Infolists\Entries;

use Filament\Actions\Action;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;
use Phiki\Grammar\Grammar;
use Phiki\Theme\Theme;

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
