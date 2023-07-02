<?php

namespace App\Http\Livewire\Infolists;

use Filament\Infolists\Components\Card;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Livewire\Component;

class Layout extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Group::make()
                    ->id('fieldset')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Fieldset::make('Rate limiting')
                            ->schema([
                                TextEntry::make('hits')
                                    ->state(30),
                                TextEntry::make('period')
                                    ->state('Hour'),
                                TextEntry::make('maximum')
                                    ->state(100),
                            ])
                            ->columns(3),
                    ]),
                Group::make()
                    ->id('tabs')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tabs\Tab::make('Rate Limiting')
                                    ->schema([
                                        TextEntry::make('hits')
                                            ->state(30),
                                        TextEntry::make('period')
                                            ->state('Hour'),
                                        TextEntry::make('maximum')
                                            ->state(100),
                                        TextEntry::make('notes')
                                            ->state('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),
                                Tabs\Tab::make('Proxy'),
                                Tabs\Tab::make('Meta'),
                            ]),
                    ]),
                Group::make()
                    ->id('tabsIcons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tabs\Tab::make('Notifications')
                                    ->icon('heroicon-m-bell')
                                    ->schema([
                                        IconEntry::make('enabled')
                                            ->boolean()
                                            ->state(true),
                                        TextEntry::make('frequency')
                                            ->state('Hourly'),
                                        TextEntry::make('notes')
                                            ->state('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Tabs\Tab::make('Security')
                                    ->icon('heroicon-m-lock-closed'),
                                Tabs\Tab::make('Meta')
                                    ->icon('heroicon-m-bars-3-center-left'),
                            ]),
                    ]),
                Group::make()
                    ->id('tabsIconsAfter')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tabs\Tab::make('Notifications')
                                    ->icon('heroicon-m-bell')
                                    ->iconPosition('after')
                                    ->schema([
                                        IconEntry::make('enabled')
                                            ->boolean()
                                            ->state(true),
                                        TextEntry::make('frequency')
                                            ->state('Hourly'),
                                        TextEntry::make('notes')
                                            ->state('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Tabs\Tab::make('Security')
                                    ->icon('heroicon-m-lock-closed')
                                    ->iconPosition('after'),
                                Tabs\Tab::make('Meta')
                                    ->icon('heroicon-m-bars-3-center-left')
                                    ->iconPosition('after'),
                            ]),
                    ]),
                Group::make()
                    ->id('tabsBadges')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Tabs::make('Tabs')
                            ->schema([
                                Tabs\Tab::make('Notifications')
                                    ->badge(5)
                                    ->schema([
                                        IconEntry::make('enabled')
                                            ->boolean()
                                            ->state(true),
                                        TextEntry::make('frequency')
                                            ->state('Hourly'),
                                        TextEntry::make('notes')
                                            ->state('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Tabs\Tab::make('Security'),
                                Tabs\Tab::make('Meta'),
                            ]),
                    ]),
                Group::make()
                    ->id('section')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Section::make('Rate limiting')
                            ->description('Prevent abuse by limiting the number of requests per period')
                            ->schema([
                                TextEntry::make('hits')
                                    ->state(30),
                                TextEntry::make('period')
                                    ->state('Hour'),
                                TextEntry::make('maximum')
                                    ->state(100),
                                TextEntry::make('notes')
                                    ->state('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.')
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
                            ->icon('heroicon-m-shopping-bag')
                            ->schema([
                                RepeatableEntry::make('items')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('product'),
                                        TextEntry::make('quantity'),
                                    ])
                                    ->columns(2),
                                TextEntry::make('specialOrderNotes')
                                    ->state('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.'),
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
                            ->schema([
                                TextEntry::make('hits')
                                    ->state(30),
                                TextEntry::make('period')
                                    ->state('Hour'),
                                TextEntry::make('maximum')
                                    ->state(100),
                                TextEntry::make('notes')
                                    ->state('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.')
                                    ->columnSpanFull(),
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
                            ->collapsed(),
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
                            ->schema([
                                TextEntry::make('hits')
                                    ->state(30),
                                TextEntry::make('period')
                                    ->state('Hour'),
                                TextEntry::make('maximum')
                                    ->state(100),
                                TextEntry::make('notes')
                                    ->state('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                    ]),
                Group::make()
                    ->id('card')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-2xl',
                    ])
                    ->schema([
                        Card::make([
                            TextEntry::make('hits')
                                ->state(30),
                            TextEntry::make('period')
                                ->state('Hour'),
                            TextEntry::make('maximum')
                                ->state(100),
                            TextEntry::make('notes')
                                ->state('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.')
                                ->columnSpanFull(),
                        ])
                            ->columns(3),
                    ]),
                Group::make()
                    ->id('split')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Split::make([
                            Card::make([
                                TextEntry::make('title')
                                    ->state('What is Filament?')
                                    ->weight('bold'),
                                TextEntry::make('content')
                                    ->state(<<<MARKDOWN
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non dui eu augue tempor finibus. Vivamus tincidunt malesuada volutpat. Donec ornare euismod est id cursus. Donec dolor nisl, dignissim vitae vulputate accumsan, consequat a lorem.

                                    Praesent nec nulla ut erat mattis tincidunt. Donec dictum nibh at consequat finibus. Donec dignissim velit euismod dui lobortis, ut cursus ante auctor. Donec id placerat felis. Nulla dolor arcu, sodales nec sapien ut, laoreet rhoncus risus. Interdum et malesuada fames ac ante ipsum primis in faucibus.
                                    MARKDOWN)
                                    ->markdown()
                                    ->prose(),
                            ]),
                            Card::make([
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->state('2022-01-01 14:10:32'),
                                TextEntry::make('published_at')
                                    ->dateTime()
                                    ->state('2023-07-02 23:29:24'),
                            ])->grow(false),
                        ]),
                    ]),
            ])
            ->state([
                'items' => [
                    ['product' => 'Filament t-shirt', 'quantity' => 3],
                    ['product' => 'Filament hoodie', 'quantity' => 1],
                ],
            ]);
    }

    public function render()
    {
        return view('livewire.infolists.layout');
    }
}
