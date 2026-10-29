<?php

namespace Filament\Tables\Enums;

enum RecordActionsPosition: string
{
    case AfterCells = 'after_cells';

    case AfterColumns = 'after_columns';

    case AfterContent = 'after_content';

    case BeforeCells = 'before_cells';

    case BeforeColumns = 'before_columns';
}
