<?php

namespace Filament\Resources\Forms\Components\Concerns;

trait InteractsWithResource
{
    public static function make($name)
    {
        return new static("record.{$name}");
    }

    public function requiredWith($field)
    {
        return parent::requiredWith("record.{$field}");
    }
}
