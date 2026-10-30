<?php

namespace Filament\Tests\Upgrade\Fixtures\StickyTraitBoundaries;

use Filament\Pages\SimplePage;

trait SharedConfiguration
{
    public static bool $formActionsAreSticky = false;

    public function disable(): void
    {
        static::$formActionsAreSticky = false;
    }
}

class SharedPage extends SimplePage
{
    use SharedConfiguration;
}

class UnrelatedConsumer
{
    use SharedConfiguration;

    public string $maxWidth = 'xl';

    public function getMaxWidth(): string
    {
        return $this->maxWidth;
    }
}

trait CollidingConfiguration
{
    public static bool $formActionsAreSticky = false;
}

class ConflictingPage extends SimplePage
{
    use CollidingConfiguration;

    public static bool $areFormActionsSticky = false;
}

trait UnusedConfiguration
{
    public static bool $formActionsAreSticky = false;
}

function configure(SharedPage $page, ConflictingPage $conflictingPage): void
{
    $page::$formActionsAreSticky = false;
    $conflictingPage::$formActionsAreSticky = true;
}
