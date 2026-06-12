<?php

namespace Filament\Tables\Enums;

enum FiltersLayout
{
    case AboveContent;

    case AboveContentCollapsible;

    case BelowContent;

    case BeforeContent;

    case AfterContent;

    case BeforeContentCollapsible;

    case AfterContentCollapsible;

    case Dropdown;

    case Modal;

    case Hidden;
}
