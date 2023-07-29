<?php

namespace Filament\Tables\Actions;

enum Position
{
    case AfterCells;

    case AfterColumns;

    case AfterContent;

    case BeforeCells;

    case BeforeColumns;

    case BottomCorner;
}
