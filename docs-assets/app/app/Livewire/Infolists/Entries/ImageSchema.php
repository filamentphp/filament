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

class ImageSchema
{
    public static function schema(): array
    {
        return [
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
                ->id('imageSize')
                ->extraAttributes([
                    'class' => 'p-16 max-w-3xl',
                ])
                ->schema([
                    ImageEntry::make('author.avatar')
                        ->imageSize(80)
                        ->state('https://picsum.photos/id/177/1200/800'),
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
                ->id('imageStackedRing')
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
                        ->ring(5),
                ]),
            Group::make()
                ->id('imageStackedOverlap')
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
                        ->overlap(2),
                ]),
        ];
    }
}
