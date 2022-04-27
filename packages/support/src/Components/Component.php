<?php

namespace Filament\Support\Components;

use Filament\Support\Concerns\Configurable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component as BaseComponent;

abstract class Component
{
    use Configurable;
    use Macroable;
    use Tappable;
}
