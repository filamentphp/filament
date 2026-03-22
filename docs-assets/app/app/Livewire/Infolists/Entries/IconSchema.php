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

class IconSchema
{
    public static function schema(): array
    {
        return [
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
        ];
    }
}
