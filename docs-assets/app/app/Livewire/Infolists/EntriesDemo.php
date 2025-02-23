<?php

namespace App\Livewire\Infolists;

use Filament\Actions\Action;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

class EntriesDemo extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->components([
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
                            ->icon(Heroicon::Envelope),
                    ]),
                Group::make()
                    ->id('textIconAfter')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('email')
                            ->state('dan@filamentphp.com')
                            ->icon(Heroicon::Envelope)
                            ->iconPosition(IconPosition::After),
                    ]),
                Group::make()
                    ->id('textIconColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('email')
                            ->state('dan@filamentphp.com')
                            ->icon(Heroicon::Envelope)
                            ->iconColor('primary'),
                    ]),
                Group::make()
                    ->id('textLarge')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('title')
                            ->state('What is Filament?')
                            ->size(TextSize::Large),
                    ]),
                Group::make()
                    ->id('textBold')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextEntry::make('title')
                            ->state('What is Filament?')
                            ->weight(FontWeight::Bold),
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
                            ->fontFamily(FontFamily::Mono),
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
                            ->icon(fn (string $state): Heroicon => match ($state) {
                                'draft' => Heroicon::OutlinedPencil,
                                'reviewing' => Heroicon::OutlinedClock,
                                'published' => Heroicon::OutlinedCheckCircle,
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
                            ->icon(fn (string $state): Heroicon => match ($state) {
                                'draft' => Heroicon::OutlinedPencil,
                                'reviewing' => Heroicon::OutlinedClock,
                                'published' => Heroicon::OutlinedCheckCircle,
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
                            ->icon(fn (string $state): Heroicon => match ($state) {
                                'draft' => Heroicon::OutlinedPencil,
                                'reviewing' => Heroicon::OutlinedClock,
                                'published' => Heroicon::OutlinedCheckCircle,
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'danger',
                                'reviewing' => 'warning',
                                'published' => 'success',
                                default => 'gray',
                            })
                            ->size(IconSize::Medium),
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
                            ->trueIcon(Heroicon::OutlinedCheckBadge)
                            ->falseIcon(Heroicon::OutlinedXMark),
                        IconEntry::make('is_featured')
                            ->state(1)
                            ->boolean()
                            ->trueIcon(Heroicon::OutlinedCheckBadge)
                            ->falseIcon(Heroicon::OutlinedXMark),
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
                            ->height(40)
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
                            ->height(40)
                            ->state('https://picsum.photos/id/433/1200/800')
                            ->circular(),
                    ]),
                Group::make()
                    ->id('imageStacked')
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
                            ->stacked(),
                    ]),
                Group::make()
                    ->id('imageLimited')
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
                            ->limit(3),
                    ]),
                Group::make()
                    ->id('imageLimitedRemainingText')
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
                            ->limitedRemainingText(),
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
                    ->id('keyValue')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        KeyValueEntry::make('meta')
                            ->state([
                                'description' => 'Filament is a collection of Laravel packages',
                                'og:type' => 'website',
                                'og:site_name' => 'Filament',
                            ]),
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
            ])
            ->constantState([
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
