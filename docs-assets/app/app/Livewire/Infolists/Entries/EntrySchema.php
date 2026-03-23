<?php

namespace App\Livewire\Infolists\Entries;

use Filament\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class EntrySchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('simple')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('title')
                        ->state('What is Filament?'),
                    TextEntry::make('author.name')
                        ->state('Dan Harrin'),
                ]),
            Group::make()
                ->id('placeholder')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('title')
                        ->placeholder('Dan Harrin'),
                ]),
            Group::make()
                ->id('inlineLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->inlineLabel()
                        ->state('Dan Harrin'),
                ]),
            Group::make()
                ->id('inlineLabelSection')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Section::make('Details')
                        ->schema([
                            TextEntry::make('name')
                                ->state('Dan Harrin'),
                            TextEntry::make('emailAddress')
                                ->state('dan@filamentphp.com'),
                            TextEntry::make('phoneNumber')
                                ->state('123-456-7890'),
                        ])
                        ->inlineLabel(),
                ]),
            Group::make()
                ->id('hiddenLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('title')
                        ->hiddenLabel()
                        ->state('What is Filament?')
                        ->size(TextSize::Large)
                        ->weight(FontWeight::Bold),
                    TextEntry::make('description')
                        ->hiddenLabel()
                        ->state('Filament is a collection of full-stack components for accelerated Laravel development.'),
                ]),
            Group::make()
                ->id('tooltips')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('title')
                        ->state('What is Filament?')
                        ->tooltip('Shown at the top of the page'),
                ]),
            Group::make()
                ->id('alignment')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->alignStart(),
                    TextEntry::make('email')
                        ->state('dan@filamentphp.com')
                        ->alignCenter(),
                    TextEntry::make('role')
                        ->state('Administrator')
                        ->alignEnd(),
                ]),
            Group::make()
                ->id('textBelowContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->belowContent('This is the user\'s full name.'),
                ]),
            Group::make()
                ->id('componentBelowContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->belowContent(Text::make('This is the user\'s full name.')->weight(FontWeight::Bold)),
                ]),
            Group::make()
                ->id('actionBelowContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->belowContent(Action::make('generate')),
                ]),
            Group::make()
                ->id('belowContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->belowContent([
                            Icon::make(Heroicon::InformationCircle),
                            'This is the user\'s full name.',
                            Action::make('generate'),
                        ]),
                ]),
            Group::make()
                ->id('belowContentAlignment')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->belowContent(Schema::end([
                            Icon::make(Heroicon::InformationCircle),
                            'This is the user\'s full name.',
                            Action::make('generate'),
                        ])),
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->belowContent(Schema::between([
                            Icon::make(Heroicon::InformationCircle),
                            'This is the user\'s full name.',
                            Action::make('generate'),
                        ])),
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->belowContent(Schema::between([
                            Flex::make([
                                Icon::make(Heroicon::InformationCircle)
                                    ->grow(false),
                                'This is the user\'s full name.',
                            ]),
                            Action::make('generate'),
                        ])),
                ]),
            Group::make()
                ->id('aboveLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->aboveLabel([
                            Icon::make(Heroicon::Star),
                            'This is the content above the entry\'s label',
                        ]),
                ]),
            Group::make()
                ->id('beforeLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->beforeLabel(Icon::make(Heroicon::Star)),
                ]),
            Group::make()
                ->id('afterLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->afterLabel([
                            Icon::make(Heroicon::Star),
                            'This is the content after the entry\'s label',
                        ]),
                ]),
            Group::make()
                ->id('afterLabelAlignedStart')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->afterLabel(Schema::start([
                            Icon::make(Heroicon::Star),
                            'This is the content after the entry\'s label',
                        ])),
                ]),
            Group::make()
                ->id('belowLabel')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->belowLabel([
                            Icon::make(Heroicon::Star),
                            'This is the content below the entry\'s label',
                        ]),
                ]),
            Group::make()
                ->id('aboveContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->belowLabel([
                            Icon::make(Heroicon::Star),
                            'This is the content above the entry\'s content',
                        ]),
                ]),
            Group::make()
                ->id('beforeContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->beforeContent(Icon::make(Heroicon::Star)),
                ]),
            Group::make()
                ->id('afterContent')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('name')
                        ->state('Dan Harrin')
                        ->afterContent(Icon::make(Heroicon::Star)),
                ]),
            Group::make()
                ->id('imageLimitedRemainingTextSeparately')
                ->extraAttributes([
                    'class' => 'p-16 max-w-3xl',
                ])
                ->schema([
                    ImageEntry::make('colleagues')
                        ->height(40)
                        ->state([
                            'https://avatars.githubusercontent.com/u/41837763?v=4',
                            'https://avatars.githubusercontent.com/u/44533235?v=4',
                            'https://avatars.githubusercontent.com/u/22632550?v=4',
                            'https://avatars.githubusercontent.com/u/3596800?v=4',
                            'https://avatars.githubusercontent.com/u/881938?v=4',
                        ])
                        ->circular()
                        ->stacked()
                        ->limit(3)
                        ->limitedRemainingText(isSeparate: true),
                ]),
            Group::make()
                ->id('suffixAction')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('suffixAction')
                        ->label('Cost')
                        ->prefix('€')
                        ->default('22.66')
                        ->suffixAction(
                            Action::make('copyCostToPrice')
                                ->icon(Heroicon::Clipboard),
                        ),
                ]),
        ];
    }
}
