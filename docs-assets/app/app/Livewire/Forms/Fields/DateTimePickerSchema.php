<?php

namespace App\Livewire\Forms\Fields;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;

class DateTimePickerSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('dateTimePickers')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DateTimePicker::make('dateTimePicker')
                        ->label('Published at'),
                    DatePicker::make('datePickers')
                        ->label('Date of birth'),
                    TimePicker::make('timePicker')
                        ->label('Alarm at'),
                ]),
            Group::make()
                ->id('dateTimePickerWithoutSeconds')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DateTimePicker::make('dateTimePickerWithoutSeconds')
                        ->label('Published at')
                        ->seconds(false),
                ]),
            Group::make()
                ->id('javascriptDateTimePicker')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('javascriptDateTimePicker')
                        ->label('Date of birth')
                        ->native(false)
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerDisplayFormat')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerDisplayFormat')
                        ->label('Date of birth')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerWeekStartsOnSunday')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerWeekStartsOnSunday')
                        ->label('Published at')
                        ->native(false)
                        ->weekStartsOnSunday()
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerDisabledDates')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerDisabledDates')
                        ->label('Date')
                        ->native(false)
                        ->disabledDates(['2000-01-03', '2000-01-15', '2000-01-20'])
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerDefaultFocusedDate')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerDefaultFocusedDate')
                        ->label('Custom starts at')
                        ->native(false)
                        ->placeholder('Jan 1, 2000')
                        ->defaultFocusedDate(Carbon::parse('2000-01-01')),
                ]),
            Group::make()
                ->id('dateTimePickerAffix')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DatePicker::make('dateTimePickerAffix')
                        ->label('Date')
                        ->prefix('Starts')
                        ->suffix('at midnight')
                        ->default('2000-01-01'),
                ]),
            Group::make()
                ->id('dateTimePickerPrefixIcon')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TimePicker::make('dateTimePickerPrefixIcon')
                        ->label('At')
                        ->prefixIcon(Heroicon::Play)
                        ->default('14:00:00'),
                ]),
            Group::make()
                ->id('dateTimePickerPrefixIconColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TimePicker::make('dateTimePickerPrefixIconColor')
                        ->label('At')
                        ->prefixIcon(Heroicon::CheckCircle)
                        ->prefixIconColor('success')
                        ->default('09:30:00'),
                ]),
        ];
    }
}
