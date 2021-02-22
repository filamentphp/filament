<?php

namespace Filament\Resources\Forms\Components\Concerns;

trait InteractsWithResource
{
    public static function make($name)
    {
        return new static('record.' . $name);
    }
}
