<?php

namespace Filament\Support\View\Components;

class Modal
{
    public static bool $hasCloseButton = true;

    public static bool $isClosedByClickingAway = true;

    public static bool $isClosedByEscaping = true;

    public static bool $isAutofocused = true;

    public static function autofocus(bool $condition = true): void
    {
        static::$isAutofocused = $condition;
    }

    public static function closeButton(bool $condition = true): void
    {
        static::$hasCloseButton = $condition;
    }

    public static function closedByClickingAway(bool $condition = true): void
    {
        static::$isClosedByClickingAway = $condition;
    }

    public static function closedByEscaping(bool $condition = true): void
    {
        static::$isClosedByEscaping = $condition;
    }
}
