<?php

namespace Filament\Support\Components;

use Filament\Support\Concerns\Configurable;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Support\Concerns\Macroable;
use Illuminate\Support\Traits\Tappable;

abstract class Component
{
    use Configurable;
    use EvaluatesClosures;
    use Macroable;
    use Tappable;
}
