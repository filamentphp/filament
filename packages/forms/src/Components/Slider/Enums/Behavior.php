<?php

namespace Filament\Forms\Components\Slider\Enums;

enum Behavior: string
{
    case Tap = 'tap';
    case Drag = 'drag';
    case Fixed = 'fixed';
    case Unconstrained = 'unconstrained';
    case SmoothSteps = 'smooth-steps';
}
