<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;

class ToggleButtonsSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('toggleButtons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtons')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->default('published'),
                ]),
            Group::make()
                ->id('toggleButtonsColors')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsColors')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->colors([
                            'draft' => 'info',
                            'scheduled' => 'warning',
                            'published' => 'success',
                        ])
                        ->default('draft'),
                ]),
            Group::make()
                ->id('toggleButtonsIcons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsIcons')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->icons([
                            'draft' => Heroicon::OutlinedPencil,
                            'scheduled' => Heroicon::OutlinedClock,
                            'published' => Heroicon::OutlinedCheckCircle,
                        ])
                        ->default('scheduled'),
                ]),
            Group::make()
                ->id('toggleButtonsHiddenLabels')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsHiddenLabels')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->icons([
                            'draft' => Heroicon::OutlinedPencil,
                            'scheduled' => Heroicon::OutlinedClock,
                            'published' => Heroicon::OutlinedCheckCircle,
                        ])
                        ->colors([
                            'draft' => 'info',
                            'scheduled' => 'warning',
                            'published' => 'success',
                        ])
                        ->hiddenButtonLabels()
                        ->default('published'),
                ]),
            Group::make()
                ->id('toggleButtonsTooltips')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsTooltips')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->icons([
                            'draft' => Heroicon::OutlinedPencil,
                            'scheduled' => Heroicon::OutlinedClock,
                            'published' => Heroicon::OutlinedCheckCircle,
                        ])
                        ->tooltips([
                            'draft' => 'Set as a draft before publishing.',
                            'scheduled' => 'Schedule publishing on a specific date.',
                            'published' => 'Publish now',
                        ])
                        ->default('draft'),
                ]),
            Group::make()
                ->id('toggleButtonsBoolean')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsBoolean')
                        ->label('Like this post?')
                        ->boolean()
                        ->default(true),
                ]),
            Group::make()
                ->id('toggleButtonsInline')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsInline')
                        ->label('Like this post?')
                        ->boolean()
                        ->inline()
                        ->default(false),
                ]),
            Group::make()
                ->id('toggleButtonsGrouped')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsGrouped')
                        ->label('Like this post?')
                        ->boolean()
                        ->grouped()
                        ->default(true),
                ]),
            Group::make()
                ->id('toggleButtonsMultiple')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsMultiple')
                        ->label('Technologies')
                        ->multiple()
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->default(['tailwind', 'laravel']),
                ]),
            Group::make()
                ->id('toggleButtonsColumns')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsColumns')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->columns(2)
                        ->default('alpine'),
                ]),
            Group::make()
                ->id('toggleButtonsRows')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('toggleButtonsRows')
                        ->label('Technologies')
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ])
                        ->columns(2)
                        ->gridDirection('row')
                        ->default('alpine'),
                ]),
            Group::make()
                ->id('disabledOptionToggleButtons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ToggleButtons::make('disabledOptionToggleButtons')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->disableOptionWhen(fn (string $value): bool => $value === 'published'),
                ]),
        ];
    }
}
