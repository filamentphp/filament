<?php

namespace Filament\Support\View\Components;

class Modal
{
    public static bool $isClosedByClickingAway = true;

    public static function closedByClickingAway(bool $condition = true): void
    {
        static::$isClosedByClickingAway = $condition;
    }
}
