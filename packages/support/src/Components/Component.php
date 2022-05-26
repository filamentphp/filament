<?php

namespace Filament\Support\Components;

use Filament\Support\Concerns\Configurable;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

abstract class Component
{
    use Configurable;
    use EvaluatesClosures;
    use Macroable;
    use Tappable;
}
