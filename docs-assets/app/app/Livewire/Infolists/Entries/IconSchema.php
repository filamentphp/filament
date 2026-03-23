<?php

namespace App\Livewire\Infolists\Entries;

use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Group;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;

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
