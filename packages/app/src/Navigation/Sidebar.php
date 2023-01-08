<?php

namespace Filament\Navigation;

class Sidebar
{
    public static bool $isCollapsibleOnDesktop = false;

    public static bool $isFullyCollapsibleOnDesktop = false;

    public static bool $hasCollapsibleGroups = false;

    public static function collapsibleOnDesktop(bool $condition = true): void
    {
        static::$isCollapsibleOnDesktop = $condition;
    }

    public static function fullyCollapsibleOnDesktop(bool $condition = true): void
    {
        static::$isFullyCollapsibleOnDesktop = $condition;
    }

    public static function collapsibleGroups(bool $condition = true): void
    {
        static::$hasCollapsibleGroups = $condition;
    }
}
