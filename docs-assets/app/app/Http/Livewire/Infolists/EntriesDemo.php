<?php

namespace App\Http\Livewire\Infolists;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Livewire\Component;

class EntriesDemo extends Component implements HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
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
                    ->id('helperText')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('name')
                            ->state('Dan Harrin')
                            ->helperText('Your **full name** here, including any middle names.'),
                    ]),
                Group::make()
                    ->id('hint')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('apiKey')
                            ->label('API key')
                            ->state('HGA3CH5AB345JD9MQ3')
                            ->hint('[Documentation](/documentation)'),
                    ]),
                Group::make()
                    ->id('hintColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('apiKey')
                            ->label('API key')
                            ->state('HGA3CH5AB345JD9MQ3')
                            ->hint('[Documentation](/documentation)')
                            ->hintColor('primary'),
                    ]),
                Group::make()
                    ->id('hintIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('apiKey')
                            ->label('API key')
                            ->state('HGA3CH5AB345JD9MQ3')
                            ->hint('[Documentation](/documentation)')
                            ->hintIcon('heroicon-m-question-mark-circle'),
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
                    ->id('text')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('title')
                            ->state('What is Filament?'),
                    ]),
                Group::make()
                    ->id('textBadge')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('status')
                            ->state('published')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'reviewing' => 'warning',
                                'published' => 'success',
                                'rejected' => 'danger',
                            }),
                    ]),
                Group::make()
                    ->id('textList')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('authors.name')
                            ->listWithLineBreaks()
                            ->state(['Dan Harrin', 'Ryan Chandler', 'Zep Fietje', 'Dennis Koch', 'Adam Weston']),
                    ]),
                Group::make()
                    ->id('textBulletList')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('authors.name')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->state(['Dan Harrin', 'Ryan Chandler', 'Zep Fietje', 'Dennis Koch', 'Adam Weston']),
                    ]),
                Group::make()
                    ->id('textColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('status')
                            ->state('Published')
                            ->color('primary'),
                    ]),
                Group::make()
                    ->id('textIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('email')
                            ->state('dan@filamentphp.com')
                            ->icon('heroicon-m-envelope'),
                    ]),
                Group::make()
                    ->id('textIconAfter')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('email')
                            ->state('dan@filamentphp.com')
                            ->icon('heroicon-m-envelope')
                            ->iconPosition('after'),
                    ]),
                Group::make()
                    ->id('textLarge')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('title')
                            ->state('What is Filament?')
                            ->size('lg'),
                    ]),
                Group::make()
                    ->id('textBold')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('title')
                            ->state('What is Filament?')
                            ->weight('bold'),
                    ]),
                Group::make()
                    ->id('textMono')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('apiKey')
                            ->label('API key')
                            ->state('HGA3CH5AB345JD9MQ3')
                            ->fontFamily('mono'),
                    ]),
                Group::make()
                    ->id('textCopyable')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('apiKey')
                            ->label('API key')
                            ->state('HGA3CH5AB345JD9MQ3')
                            ->copyable()
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                    ]),
                Group::make()
                    ->id('icon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        IconEntry::make('status')
                            ->state('reviewing')
                            ->icon(fn (string $state): string => match ($state) {
                                'draft' => 'heroicon-o-pencil',
                                'reviewing' => 'heroicon-o-clock',
                                'published' => 'heroicon-o-check-circle',
                            }),
                    ]),
                Group::make()
                    ->id('iconColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        IconEntry::make('status')
                            ->state('reviewing')
                            ->icon(fn (string $state): string => match ($state) {
                                'draft' => 'heroicon-o-pencil',
                                'reviewing' => 'heroicon-o-clock',
                                'published' => 'heroicon-o-check-circle',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'info',
                                'reviewing' => 'warning',
                                'published' => 'success',
                                default => 'gray',
                            }),
                    ]),
                Group::make()
                    ->id('iconMedium')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        IconEntry::make('status')
                            ->state('reviewing')
                            ->icon(fn (string $state): string => match ($state) {
                                'draft' => 'heroicon-o-pencil',
                                'reviewing' => 'heroicon-o-clock',
                                'published' => 'heroicon-o-check-circle',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'danger',
                                'reviewing' => 'warning',
                                'published' => 'success',
                                default => 'gray',
                            })
                            ->size('md'),
                    ]),
                Group::make()
                    ->id('iconBoolean')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        IconEntry::make('is_featured')
                            ->state(0)
                            ->boolean(),
                        IconEntry::make('is_featured')
                            ->state(1)
                            ->boolean(),
                    ]),
                Group::make()
                    ->id('iconBooleanIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        IconEntry::make('is_featured')
                            ->state(0)
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-mark'),
                        IconEntry::make('is_featured')
                            ->state(1)
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-mark'),
                    ]),
                Group::make()
                    ->id('iconBooleanColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        IconEntry::make('is_featured')
                            ->state(0)
                            ->boolean()
                            ->trueColor('info')
                            ->falseColor('warning'),
                        IconEntry::make('is_featured')
                            ->state(1)
                            ->boolean()
                            ->trueColor('info')
                            ->falseColor('warning'),
                    ]),
                Group::make()
                    ->id('image')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->schema([
                        ImageEntry::make('header_image')
                            ->state('https://picsum.photos/id/12/1200/800'),
                    ]),
                Group::make()
                    ->id('imageSquare')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->schema([
                        ImageEntry::make('author.avatar')
                            ->state('https://picsum.photos/id/177/1200/800')
                            ->square(),
                    ]),
                Group::make()
                    ->id('imageCircular')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->schema([
                        ImageEntry::make('author.avatar')
                            ->state('https://picsum.photos/id/433/1200/800')
                            ->circular(),
                    ]),
                Group::make()
                    ->id('color')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ColorEntry::make('color')
                            ->state('#3490dc'),
                    ]),
                Group::make()
                    ->id('colorCopyable')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ColorEntry::make('color')
                            ->state('#3490dc')
                            ->copyable()
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                    ]),
                Group::make()
                    ->id('repeatable')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        RepeatableEntry::make('comments')
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
                            ->schema([
                                TextEntry::make('author'),
                                TextEntry::make('title'),
                                TextEntry::make('content')
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->grid(2),
                    ]),
            ])
            ->state([
                'comments' => [
                    [
                        'author' => ['name' => 'Jane Doe'],
                        'title' => 'Wow!',
                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.',
                    ],
                    [
                        'author' => ['name' => 'John Doe'],
                        'title' => 'This isn\'t working. Help!',
                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.',
                    ],
                ],
            ]);
    }

    public function render()
    {
        return view('livewire.infolists.entries');
    }
}
