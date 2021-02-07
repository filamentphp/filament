<?php

namespace Filament\Fields;

use Filament\Traits\FieldConcerns;
use Illuminate\Support\Str;

class Tab extends Field
{
    use FieldConcerns\CanHaveId;
    use FieldConcerns\CanHaveLabel;

    public static function make($label)
    {
        return (new static())
            ->label($label)
            ->id((string) Str::of($label)->slug());
    }
}
