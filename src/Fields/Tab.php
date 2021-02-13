<?php

namespace Filament\Fields;

use Illuminate\Support\Str;

class Tab extends Field
{
    public static function make($label)
    {
        return (new static())
            ->label($label)
            ->id(Str::slug($label));
    }
}
