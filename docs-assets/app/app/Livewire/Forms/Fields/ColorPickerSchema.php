<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Group;

class ColorPickerSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('colorPicker')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    ColorPicker::make('colorPicker')
                        ->label('Color')
                        ->default('#3490dc'),
                ]),
            Group::make()
                ->id('colorPickerOpen')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    ColorPicker::make('colorPickerOpen')
                        ->label('Color')
                        ->default('#e3342f'),
                ]),
            Group::make()
                ->id('colorPickerFormats')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    ColorPicker::make('hsl_color')
                        ->label('HSL')
                        ->hsl()
                        ->default('hsl(210, 68%, 53%)'),
                    ColorPicker::make('rgb_color')
                        ->label('RGB')
                        ->rgb()
                        ->default('rgb(52, 144, 220)'),
                    ColorPicker::make('rgba_color')
                        ->label('RGBA')
                        ->rgba()
                        ->default('rgba(52, 144, 220, 0.5)'),
                ]),
        ];
    }
}
