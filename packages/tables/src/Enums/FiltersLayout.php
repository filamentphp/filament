<?php

namespace Filament\Tables\Enums;

enum FiltersLayout: string
{
    case AboveContent = 'above_content';

    case AboveContentCollapsible = 'above_content_collapsible';

    case BelowContent = 'below_content';

    case BeforeContent = 'before_content';

    case AfterContent = 'after_content';

    case BeforeContentCollapsible = 'before_content_collapsible';

    case AfterContentCollapsible = 'after_content_collapsible';

    case Dropdown = 'dropdown';

    case Modal = 'modal';

    case Hidden = 'hidden';
}
