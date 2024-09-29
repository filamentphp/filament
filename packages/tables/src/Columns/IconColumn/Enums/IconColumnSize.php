<?php

namespace Filament\Tables\Columns\IconColumn\Enums;

enum IconColumnSize: string
{
    case ExtraSmall = 'xs';

    case Small = 'sm';

    case Medium = 'md';

    case Large = 'lg';

    case ExtraLarge = 'xl';

    case TwoExtraLarge = '2xl';

    /**
     * @deprecated Use `TwoExtraLarge` instead.
     */
    public const ExtraExtraLarge = self::TwoExtraLarge;
}
