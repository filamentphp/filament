<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Group;

class TagsInputSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('tagsInput')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TagsInput::make('tagsInput')
                        ->label('Tags')
                        ->default(['Tailwind CSS', 'Alpine.js']),
                ]),
            Group::make()
                ->id('tagsInputTagPrefix')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TagsInput::make('tagsInputTagPrefix')
                        ->label('Hashtags')
                        ->tagPrefix('#')
                        ->default(['filament', 'laravel', 'livewire']),
                ]),
            Group::make()
                ->id('tagsInputColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TagsInput::make('tagsInputColor')
                        ->label('Tags')
                        ->color('danger')
                        ->default(['urgent', 'critical', 'review']),
                ]),
        ];
    }
}
