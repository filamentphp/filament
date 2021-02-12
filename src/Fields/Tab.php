<?php

namespace Filament\Fields;

use Filament\Fields\Concerns;
use Illuminate\Support\Str;

class Tab extends Field
{
    use Concerns\HasId;
    use Concerns\HasLabel;

    public static function make($label)
    {
        return (new static())
            ->label($label)
            ->id((string) Str::of($label)->slug());
    }
}
