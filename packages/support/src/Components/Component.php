<?php

namespace Filament\Support\Components;

use Filament\Support\Concerns\Configurable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

abstract class Component
{
    use Configurable;
    use Macroable;
    use Tappable;
}
