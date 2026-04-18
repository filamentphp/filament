<?php

namespace App\Livewire\Schemas\Layout;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;

class FieldsetSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('fieldset')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Fieldset::make('Rate limiting')
                        ->statePath('fieldset')
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
                ]),
            Group::make()
                ->id('fieldsetNotContained')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Fieldset::make('Rate limiting')
                        ->statePath('fieldsetNotContained')
                        ->contained(false)
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
                ]),
        ];
    }
}
