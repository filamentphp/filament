<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;

class SelectSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('select')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Select::make('select')
                        ->label('Status'),
                ]),
            Group::make()
                ->id('javascriptSelect')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-48 max-w-xl',
                ])
                ->schema([
                    Select::make('javascriptSelect')
                        ->label('Status')
                        ->native(false)
                        ->options([
                            'draft' => 'Draft',
                            'reviewing' => 'Reviewing',
                            'published' => 'Published',
                        ]),
                ]),
            Group::make()
                ->id('searchableSelect')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-72 max-w-xl',
                ])
                ->schema([
                    Select::make('searchableSelect')
                        ->label('Author')
                        ->searchable()
                        ->options([
                            'dan' => 'Dan Harrin',
                            'ryan' => 'Ryan Chandler',
                            'zep' => 'Zep Fietje',
                            'dennis' => 'Dennis Koch',
                            'adam' => 'Adam Weston',
                        ]),
                ]),
            Group::make()
                ->id('multipleSelect')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-44 max-w-xl',
                ])
                ->schema([
                    Select::make('multipleSelect')
                        ->label('Technologies')
                        ->multiple()
                        ->options([
                            'tailwind' => 'Tailwind CSS',
                            'alpine' => 'Alpine.js',
                            'laravel' => 'Laravel',
                            'livewire' => 'Laravel Livewire',
                        ]),
                ]),
            Group::make()
                ->id('groupedSelect')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    Select::make('groupedSelect')
                        ->label('Status')
                        ->searchable()
                        ->options([
                            'In Process' => [
                                'draft' => 'Draft',
                                'reviewing' => 'Reviewing',
                            ],
                            'Reviewed' => [
                                'published' => 'Published',
                                'rejected' => 'Rejected',
                            ],
                        ]),
                ]),
            Group::make()
                ->id('createSelectOption')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Select::make('createSelectOption')
                        ->label('Author')
                        ->createOptionForm([
                            TextInput::make('name'),
                        ]),
                ]),
            Group::make()
                ->id('editSelectOption')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Select::make('editSelectOption')
                        ->label('Author')
                        ->default('dan')
                        ->options([
                            'dan' => 'Dan Harrin',
                        ])
                        ->fillEditOptionActionFormUsing(fn () => ['name' => 'Dan Harrin'])
                        ->editOptionForm([
                            TextInput::make('name'),
                        ]),
                ]),
            Group::make()
                ->id('selectAffix')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Select::make('selectAffix')
                        ->label('Domain')
                        ->default('filament')
                        ->options([
                            'filament' => 'filamentphp',
                        ])
                        ->prefix('https://')
                        ->suffix('.com'),
                ]),
            Group::make()
                ->id('selectSuffixIcon')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Select::make('selectSuffixIcon')
                        ->label('Domain')
                        ->default('filament')
                        ->options([
                            'filament' => 'filamentphp',
                        ])
                        ->suffixIcon(Heroicon::GlobeAlt),
                ]),
            Group::make()
                ->id('selectSuffixIconColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Select::make('selectSuffixIconColor')
                        ->label('Domain')
                        ->default('filament')
                        ->options([
                            'filament' => 'filamentphp',
                        ])
                        ->suffixIcon(Heroicon::CheckCircle)
                        ->suffixIconColor('success'),
                ]),
            Group::make()
                ->id('selectBoolean')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Select::make('selectBoolean')
                        ->label('Like this post?')
                        ->boolean()
                        ->default(true),
                ]),
            Group::make()
                ->id('selectDisabledOptions')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-48 max-w-xl',
                ])
                ->schema([
                    Select::make('selectDisabledOptions')
                        ->label('Status')
                        ->native(false)
                        ->options([
                            'draft' => 'Draft',
                            'reviewing' => 'Reviewing',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->disableOptionWhen(fn (string $value): bool => $value === 'published'),
                ]),
            Group::make()
                ->id('selectHtmlLabels')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    Select::make('selectHtmlLabels')
                        ->label('Technology')
                        ->options([
                            'tailwind' => '<span style="color: #3b82f6; font-weight: 600;">Tailwind CSS</span>',
                            'alpine' => '<span style="color: #22c55e; font-weight: 600;">Alpine.js</span>',
                            'laravel' => '<span style="color: #ef4444; font-weight: 600;">Laravel</span>',
                            'livewire' => '<span style="color: #ec4899; font-weight: 600;">Livewire</span>',
                        ])
                        ->searchable()
                        ->allowHtml(),
                ]),
            Group::make()
                ->id('selectTruncateLabels')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 max-w-xl',
                    'style' => 'padding-bottom: 18rem',
                ])
                ->schema([
                    Select::make('selectTruncateLabels')
                        ->label('Framework')
                        ->options([
                            'tailwind' => 'Tailwind CSS - A utility-first CSS framework for rapid UI development',
                            'alpine' => 'Alpine.js - A lightweight JavaScript framework for composing behavior',
                            'laravel' => 'Laravel - A PHP web application framework with expressive, elegant syntax',
                            'livewire' => 'Livewire - A full-stack framework for building dynamic interfaces',
                        ])
                        ->searchable()
                        ->wrapOptionLabels(false),
                ]),
        ];
    }
}
