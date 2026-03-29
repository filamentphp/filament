<?php

namespace App\Livewire\Infolists\Entries;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class TextSchema
{
    public static function schema(): array
    {
        return [
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
                ->id('textSeparatorBadge')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('tags')
                        ->state('Laravel, Livewire, Filament, Alpine.js')
                        ->badge()
                        ->separator(','),
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
                ->id('textExpandableLimitedList')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('authors.name')
                        ->listWithLineBreaks()
                        ->limitList(2)
                        ->expandableLimitedList()
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
                ->id('textLimit')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('description')
                        ->state('Filament is a collection of full-stack components for accelerated Laravel development. They are beautifully designed, intuitive to use, and fully extensible.')
                        ->limit(50),
                ]),
            Group::make()
                ->id('textWords')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('description')
                        ->state('Filament is a collection of full-stack components for accelerated Laravel development. They are beautifully designed, intuitive to use, and fully extensible.')
                        ->words(10),
                ]),
            Group::make()
                ->id('textWrap')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('description')
                        ->state('Filament is a collection of full-stack components for accelerated Laravel development. They are beautifully designed, intuitive to use, and fully extensible. Build admin panels, customer-facing apps, and SaaS platforms with ease.')
                        ->wrap(),
                ]),
            Group::make()
                ->id('textLineClamp')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('description')
                        ->state('Filament is a collection of full-stack components for accelerated Laravel development. They are beautifully designed, intuitive to use, and fully extensible. Build admin panels, customer-facing apps, and SaaS platforms with ease.')
                        ->wrap()
                        ->lineClamp(2),
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
                ->id('textNumeric')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('stock')
                        ->state(1234567)
                        ->numeric(),
                ]),
            Group::make()
                ->id('textMoney')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('price')
                        ->state(12999)
                        ->money('USD', divideBy: 100),
                ]),
            Group::make()
                ->id('textDate')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('created_at')
                        ->state('2025-01-15 09:30:00')
                        ->dateTime(),
                ]),
            Group::make()
                ->id('textSince')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('created_at')
                        ->state('2024-06-15 09:30:00')
                        ->since(),
                ]),
            Group::make()
                ->id('textDateTooltip')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('created_at')
                        ->state('2024-06-15 09:30:00')
                        ->since()
                        ->dateTooltip(),
                ]),
            Group::make()
                ->id('textMarkdown')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('description')
                        ->state("Filament is a **full-stack** UI framework for [Laravel](https://laravel.com). It provides:\n\n- Admin panels\n- Form builder\n- Table builder\n- *And much more*")
                        ->markdown(),
                ]),
            Group::make()
                ->id('textHtml')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextEntry::make('description')
                        ->state('Filament is a <strong>full-stack</strong> UI framework for <a href="https://laravel.com">Laravel</a>. It provides <em>beautiful</em> admin panels, <u>form builders</u>, and table components.')
                        ->html(),
                ]),
        ];
    }
}
