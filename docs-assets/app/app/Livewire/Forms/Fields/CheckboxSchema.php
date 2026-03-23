<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Group;

class CheckboxSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('checkbox')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Checkbox::make('checkbox')
                        ->label('Is admin'),
                ]),
            Group::make()
                ->id('inlineCheckbox')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Checkbox::make('inlineCheckbox')
                        ->label('Is admin')
                        ->inline(),
                ]),
            Group::make()
                ->id('notInlineCheckbox')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Checkbox::make('notInlineCheckbox')
                        ->label('Is admin')
                        ->inline(false),
                ]),
        ];
    }
}
