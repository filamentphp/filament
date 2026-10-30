<?php

namespace Filament\Tests\Upgrade\Fixtures\StickyCrossFile;

trait StickyConfiguration
{
    public static bool $formActionsAreSticky = false;

    public static function disable(): void
    {
        self::$formActionsAreSticky = false;
    }

    public function enable(): void
    {
        $this::$formActionsAreSticky = true;
    }
}

trait NestedConfiguration
{
    use StickyConfiguration;

    public function configuredSticky(): bool
    {
        return static::$formActionsAreSticky;
    }
}
