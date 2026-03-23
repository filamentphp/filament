<?php

namespace App\Livewire\Schemas\Layout;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

class TabsSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('tabs')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Tabs::make('Tabs')
                        ->statePath('tabs')
                        ->schema([
                            Tab::make('Rate Limiting')
                                ->schema([
                                    TextInput::make('hits')
                                        ->default(30),
                                    Select::make('period')
                                        ->default('hour')
                                        ->options([
                                            'hour' => 'Hour',
                                        ]),
                                    TextInput::make('maximum')
                                        ->default(100),
                                    Textarea::make('notes')
                                        ->columnSpanFull(),
                                ])
                                ->columns(3),
                            Tab::make('Proxy'),
                            Tab::make('Meta'),
                        ]),
                ]),
            Group::make()
                ->id('tabsIcons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Tabs::make('Tabs')
                        ->statePath('tabsIcons')
                        ->schema([
                            Tab::make('Notifications')
                                ->icon(Heroicon::Bell)
                                ->schema([
                                    Checkbox::make('enabled')
                                        ->default(true),
                                    Select::make('frequency')
                                        ->default('hourly')
                                        ->options([
                                            'hourly' => 'Hourly',
                                        ]),
                                ]),
                            Tab::make('Security')
                                ->icon(Heroicon::LockClosed),
                            Tab::make('Meta')
                                ->icon(Heroicon::Bars3CenterLeft),
                        ]),
                ]),
            Group::make()
                ->id('tabsIconsAfter')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Tabs::make('Tabs')
                        ->statePath('tabsIconsAfter')
                        ->schema([
                            Tab::make('Notifications')
                                ->icon(Heroicon::Bell)
                                ->iconPosition(IconPosition::After)
                                ->schema([
                                    Checkbox::make('enabled')
                                        ->default(true),
                                    Select::make('frequency')
                                        ->default('hourly')
                                        ->options([
                                            'hourly' => 'Hourly',
                                        ]),
                                ]),
                            Tab::make('Security')
                                ->icon(Heroicon::LockClosed)
                                ->iconPosition(IconPosition::After),
                            Tab::make('Meta')
                                ->icon(Heroicon::Bars3CenterLeft)
                                ->iconPosition(IconPosition::After),
                        ]),
                ]),
            Group::make()
                ->id('tabsBadges')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Tabs::make('Tabs')
                        ->statePath('tabsBadges')
                        ->schema([
                            Tab::make('Notifications')
                                ->badge(5)
                                ->schema([
                                    Checkbox::make('enabled')
                                        ->default(true),
                                    Select::make('frequency')
                                        ->default('hourly')
                                        ->options([
                                            'hourly' => 'Hourly',
                                        ]),
                                ]),
                            Tab::make('Security'),
                            Tab::make('Meta'),
                        ]),
                ]),
            Group::make()
                ->id('tabsBadgesColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Tabs::make('Tabs')
                        ->statePath('tabsBadgesColor')
                        ->schema([
                            Tab::make('Notifications')
                                ->badge(5)
                                ->badgeColor('info')
                                ->schema([
                                    Checkbox::make('enabled')
                                        ->default(true),
                                    Select::make('frequency')
                                        ->default('hourly')
                                        ->options([
                                            'hourly' => 'Hourly',
                                        ]),
                                ]),
                            Tab::make('Security'),
                            Tab::make('Meta'),
                        ]),
                ]),
            Group::make()
                ->id('tabsVertical')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Tabs::make('Tabs')
                        ->statePath('tabsVertical')
                        ->schema([
                            Tab::make('Rate Limiting')
                                ->schema([
                                    TextInput::make('hits')
                                        ->default(30),
                                    Select::make('period')
                                        ->default('hour')
                                        ->options([
                                            'hour' => 'Hour',
                                        ]),
                                    TextInput::make('maximum')
                                        ->default(100),
                                    Textarea::make('notes')
                                        ->columnSpanFull(),
                                ])
                                ->columns(3),
                            Tab::make('Proxy'),
                            Tab::make('Meta'),
                        ])
                        ->vertical(),
                ]),
            Group::make()
                ->id('tabsNotScrollable')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Tabs::make('Tabs')
                        ->statePath('tabsNotScrollable')
                        ->schema([
                            Tab::make('Tab 1')
                                ->schema([
                                    TextInput::make('field'),
                                ]),
                            Tab::make('Tab 2'),
                            Tab::make('Tab 3'),
                            Tab::make('Tab 4'),
                            Tab::make('Tab 5'),
                            Tab::make('Tab 6'),
                            Tab::make('Tab 7'),
                        ])
                        ->scrollable(false),
                ]),
            Group::make()
                ->id('tabsNotContained')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Tabs::make('Tabs')
                        ->statePath('tabsNotContained')
                        ->schema([
                            Tab::make('Rate Limiting')
                                ->schema([
                                    TextInput::make('hits')
                                        ->default(30),
                                    Select::make('period')
                                        ->default('hour')
                                        ->options([
                                            'hour' => 'Hour',
                                        ]),
                                    TextInput::make('maximum')
                                        ->default(100),
                                ])
                                ->columns(3),
                            Tab::make('Proxy'),
                            Tab::make('Meta'),
                        ])
                        ->contained(false),
                ]),
        ];
    }
}
