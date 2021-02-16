<?php

namespace Filament\Resources\Fields\Concerns;

trait PreparesFieldForResourceUse
{
    public static function make($name)
    {
        return new static('record.'.$name);
    }
}
