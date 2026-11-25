<?php

namespace Filament\Forms\Components\DateRangePicker\Enums;

use Filament\Support\Contracts\HasLabel;

enum Preset: string implements HasLabel
{
    case Today = 'today';

    case Yesterday = 'yesterday';

    case WeekToDate = 'week_to_date';

    case PastSevenDays = 'past_7_days';

    case PastThirtyDays = 'past_30_days';

    case MonthToDate = 'month_to_date';

    case PreviousMonth = 'previous_month';

    case YearToDate = 'year_to_date';

    public function getLabel(): string
    {
        return match ($this) {
            self::Today => __('filament-forms::components.date_range_picker.presets.today.label'),
            self::Yesterday => __('filament-forms::components.date_range_picker.presets.yesterday.label'),
            self::WeekToDate => __('filament-forms::components.date_range_picker.presets.week_to_date.label'),
            self::PastSevenDays => __('filament-forms::components.date_range_picker.presets.past_7_days.label'),
            self::PastThirtyDays => __('filament-forms::components.date_range_picker.presets.past_30_days.label'),
            self::MonthToDate => __('filament-forms::components.date_range_picker.presets.month_to_date.label'),
            self::PreviousMonth => __('filament-forms::components.date_range_picker.presets.previous_month.label'),
            self::YearToDate => __('filament-forms::components.date_range_picker.presets.year_to_date.label'),
        };
    }
}
