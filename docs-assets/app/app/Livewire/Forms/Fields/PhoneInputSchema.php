<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\PhoneInput;
use Filament\Schemas\Components\Group;

class PhoneInputSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('phoneInput')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    PhoneInput::make('phoneInput')
                        ->label('Phone')
                        ->defaultCountry('US')
                        ->default('+12015550123'),
                ]),
            Group::make()
                ->id('phoneInputCountries')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-80 max-w-xl',
                ])
                ->schema([
                    PhoneInput::make('phoneInputCountries')
                        ->label('Phone')
                        ->defaultCountry('PT')
                        ->preferredCountries(['PT', 'BR', 'US'])
                        ->default('+351912345678'),
                ]),
        ];
    }
}
