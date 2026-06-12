<?php

namespace App\Livewire\Infolists\Entries;

use Filament\Infolists\Components\ColorEntry;
use Filament\Schemas\Components\Group;

class ColorSchema
{
    public static function schema(): array
    {
        return [
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
        ];
    }
}
