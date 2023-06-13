<?php

namespace Filament\Support\View\Components;

class Modal
{
    public static bool $isCloseButtonDisplayed = true;

    public static bool $isClosedByClickingAway = true;

    public static function closeButtonDisplayed(bool $condition = true): void
    {
        static::$isCloseButtonDisplayed = $condition;
    }

    public static function closedByClickingAway(bool $condition = true): void
    {
        static::$isClosedByClickingAway = $condition;
    }
}
