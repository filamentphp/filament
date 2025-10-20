<?php

namespace Filament\Forms\Components\Slider\Enums;

enum PipsMode: string
{
    case Range = 'range';
    case Steps = 'steps';
    case Positions = 'positions';
    case Count = 'count';
    case Values = 'values';
}
