<?php

namespace App\Livewire\Infolists\Entries;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Components\Group;

class KeyValueSchema
{
    public static function schema(): array
    {
        return [
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
                ->id('keyValueCustomLabels')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    KeyValueEntry::make('meta')
                        ->keyLabel('Property name')
                        ->valueLabel('Property value')
                        ->state([
                            'description' => 'Filament is a collection of Laravel packages',
                            'og:type' => 'website',
                            'og:site_name' => 'Filament',
                        ]),
                ]),
        ];
    }
}
