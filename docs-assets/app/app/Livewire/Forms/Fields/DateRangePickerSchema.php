<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\DateRangePicker;
use Filament\Forms\Components\DateRangePicker\Enums\Preset;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;

class DateRangePickerSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('dateRangePicker')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-4xl',
                ])
                ->schema([
                    DateRangePicker::make('dateRangePicker')
                        ->label('Campaign dates')
                        ->default(['start' => '2026-03-09', 'end' => '2026-03-20']),
                ]),
            Group::make()
                ->id('dateRangePickerPresets')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-5xl',
                ])
                ->schema([
                    DateRangePicker::make('dateRangePickerPresets')
                        ->label('Reporting period')
                        ->presets([
                            Preset::Today,
                            Preset::Yesterday,
                            Preset::PastSevenDays,
                            Preset::PastThirtyDays,
                            'Q1 2026' => ['2026-01-01', '2026-03-31'],
                            'Spring sale' => ['2026-03-20', '2026-04-05'],
                        ])
                        ->default(['start' => '2026-03-20', 'end' => '2026-04-05']),
                ]),
            Group::make()
                ->id('dateRangePickerDisplayFormat')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DateRangePicker::make('dateRangePickerDisplayFormat')
                        ->label('Campaign dates')
                        ->displayFormat('d/m/Y')
                        ->default(['start' => '2026-03-09', 'end' => '2026-03-20']),
                ]),
            Group::make()
                ->id('dateRangePickerDisabledDates')
                ->extraAttributes([
                    'class' => 'px-16 pt-16 pb-96 max-w-4xl',
                ])
                ->schema([
                    DateRangePicker::make('dateRangePickerDisabledDates')
                        ->label('Stay')
                        ->disabledDates(['2026-12-24', '2026-12-25', '2026-12-31', '2027-01-01'])
                        ->default(['start' => '2026-12-18', 'end' => '2026-12-22']),
                ]),
            Group::make()
                ->id('dateRangePickerPrefixIcon')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    DateRangePicker::make('dateRangePickerPrefixIcon')
                        ->label('Campaign dates')
                        ->prefixIcon(Heroicon::CalendarDateRange)
                        ->default(['start' => '2026-03-09', 'end' => '2026-03-20']),
                ]),
        ];
    }
}
