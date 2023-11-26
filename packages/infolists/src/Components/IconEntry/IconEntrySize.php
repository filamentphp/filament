<?php

namespace Filament\Infolists\Components\IconEntry;

enum IconEntrySize
{
    case ExtraSmall;

    case Small;

    case Medium;

    case Large;

    case ExtraLarge;

    case TwoExtraLarge;

    public const ExtraExtraLarge = self::TwoExtraLarge;
}
