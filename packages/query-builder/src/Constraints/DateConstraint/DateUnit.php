<?php

namespace Filament\QueryBuilder\Constraints\DateConstraint;

use Filament\Support\Contracts\HasLabel;

enum DateUnit: string implements HasLabel
{
    case Second = 'second';
    case Minute = 'minute';
    case Hour = 'hour';
    case Day = 'day';
    case Week = 'week';
    case Month = 'month';
    case Quarter = 'quarter';
    case Year = 'year';

    public function getLabel(): string
    {
        return match ($this) {
            self::Second => __('filament-query-builder::query-builder.operators.date.unit_labels.second'),
            self::Minute => __('filament-query-builder::query-builder.operators.date.unit_labels.minute'),
            self::Hour => __('filament-query-builder::query-builder.operators.date.unit_labels.hour'),
            self::Day => __('filament-query-builder::query-builder.operators.date.unit_labels.day'),
            self::Week => __('filament-query-builder::query-builder.operators.date.unit_labels.week'),
            self::Month => __('filament-query-builder::query-builder.operators.date.unit_labels.month'),
            self::Quarter => __('filament-query-builder::query-builder.operators.date.unit_labels.quarter'),
            self::Year => __('filament-query-builder::query-builder.operators.date.unit_labels.year'),
        };
    }

    public function isTimeUnit(): bool
    {
        return in_array($this, [self::Second, self::Minute, self::Hour], strict: true);
    }
}
