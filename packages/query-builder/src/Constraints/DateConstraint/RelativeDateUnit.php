<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint;

use Filament\Support\Contracts\HasLabel;

enum RelativeDateUnit: string implements HasLabel
{
    case Day = 'day';
    case Week = 'week';
    case Month = 'month';
    case Quarter = 'quarter';
    case Year = 'year';

    public function getLabel(): string
    {
        return match ($this) {
            self::Day => __('filament-query-builder::query-builder.operators.date.units.day'),
            self::Week => __('filament-query-builder::query-builder.operators.date.units.week'),
            self::Month => __('filament-query-builder::query-builder.operators.date.units.month'),
            self::Quarter => __('filament-query-builder::query-builder.operators.date.units.quarter'),
            self::Year => __('filament-query-builder::query-builder.operators.date.units.year'),
        };
    }
}
