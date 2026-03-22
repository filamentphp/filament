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
