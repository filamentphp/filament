<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Group;

class KeyValueSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('keyValue')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    KeyValue::make('keyValue')
                        ->label('Meta')
                        ->default([
                            'description' => 'Filament is a collection of Laravel packages',
                            'og:type' => 'website',
                            'og:site_name' => 'Filament',
                        ]),
                ]),
            Group::make()
                ->id('reorderableKeyValue')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    KeyValue::make('reorderableKeyValue')
                        ->label('Meta')
                        ->default([
                            'description' => 'Filament is a collection of Laravel packages',
                            'og:type' => 'website',
                            'og:site_name' => 'Filament',
                        ])
                        ->reorderable(),
                ]),
            Group::make()
                ->id('keyValueCustomLabels')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    KeyValue::make('keyValueCustomLabels')
                        ->label('Environment Variables')
                        ->keyLabel('Variable')
                        ->valueLabel('Value')
                        ->keyPlaceholder('e.g. APP_NAME')
                        ->valuePlaceholder('e.g. My Application')
                        ->default([
                            'APP_NAME' => 'Filament',
                            'APP_ENV' => 'production',
                            'APP_DEBUG' => 'false',
                        ]),
                ]),
        ];
    }
}
