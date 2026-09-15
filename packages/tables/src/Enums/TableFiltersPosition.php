<?php

namespace Filament\Tables\Enums;

/**
 * @internal Used by the default table layout to place the filters form for each `FiltersLayout`.
 */
enum TableFiltersPosition
{
    case Auto;

    case Before;

    case Above;

    case Below;

    case After;
}
