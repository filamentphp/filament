<?php

namespace Filament\Resources\Fields\Concerns;

trait InteractsWithResource
{
    public static function make($name)
    {
        return new static('record.' . $name);
    }
}
