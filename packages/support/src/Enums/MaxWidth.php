<?php

namespace Filament\Support\Enums;

enum MaxWidth: string
{
    case Zero = 'max-w-0';

    case None = 'max-w-none';

    case ExtraSmall = 'max-w-xs';

    case Small  = 'max-w-sm';

    case Medium = 'max-w-md';

    case Large = 'max-w-lg';

    case ExtraLarge = 'max-w-xl';

    case TwoExtraLarge = 'max-w-2xl';

    case ThreeExtraLarge = 'max-w-3xl';

    case FourExtraLarge = 'max-w-4xl';

    case FiveExtraLarge = 'max-w-5xl';

    case SixExtraLarge = 'max-w-6xl';

    case SevenExtraLarge = 'max-w-7xl';

    case Full = 'max-w-full';

    case MinContent = 'max-w-min';

    case MaxContent = 'max-w-max';

    case FitContent = 'max-w-fit';

    case Prose = 'max-w-prose';

    case ScreenSmall = 'max-w-screen-sm';

    case ScreenMedium = 'max-w-screen-md';

    case ScreenLarge = 'max-w-screen-lg';

    case ScreenExtraLarge = 'max-w-screen-xl';

    case ScreenTwoExtraLarge = 'max-w-screen-2xl';
}
