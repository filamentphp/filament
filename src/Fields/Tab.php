<?php

namespace Filament\Fields;

use Filament\FieldConcerns;
use Illuminate\Support\Str;

class Tab extends Field
{
    use FieldConcerns\HasId;
    use FieldConcerns\HasLabel;

    public static function make($label)
    {
        return (new static())
            ->label($label)
            ->id((string) Str::of($label)->slug());
    }
}
