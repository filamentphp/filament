<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\HtmlString;

class MultiSelect extends Select
{
    public function isMultiple(): bool
    {
        return true;
    }
}
