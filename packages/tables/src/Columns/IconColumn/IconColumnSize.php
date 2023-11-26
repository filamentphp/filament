<?php

namespace Filament\Tables\Columns\IconColumn;

enum IconColumnSize
{
    case ExtraSmall;

    case Small;

    case Medium;

    case Large;

    case ExtraLarge;

    case TwoExtraLarge;

    public const ExtraExtraLarge = self::TwoExtraLarge;
}
