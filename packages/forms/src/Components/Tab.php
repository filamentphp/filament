<?php

namespace Filament\Forms\Components;

use Illuminate\Support\Str;

class Tab extends Component
{
    public static function make($label)
    {
        return (new static())
            ->label($label)
            ->id(Str::slug($label));
    }
}
