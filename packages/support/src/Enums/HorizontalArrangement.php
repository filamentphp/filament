<?php

namespace Filament\Support\Enums;

enum HorizontalArrangement: string
{
    case Start = 'start';

    case End = 'end';

    case Center = 'center';

    case Between = 'between';

    case Around = 'around';

    case Evenly = 'evenly';

    case Stretch = 'stretch';

    case Baseline = 'baseline';
}
