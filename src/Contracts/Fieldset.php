<?php

namespace Filament\Contracts;

interface Fieldset
{
    public static function title(): string;

    public static function fields($model): array;
}